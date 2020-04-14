<?php

namespace kosuha606\Minidoc;

use kosuha606\Minidoc\dto\ResourceDTO;
use DocBlockReader\Reader;
use Exception;

/**
 * Class DocsBuilder
 * @package app\contexts\Application\Docs\package
 * @category Строитель
 */
class DocsBuilder
{
    private $searchQuery = false;

    /**
     * @var string
     */
    private $language = 'en';

    /**
     * @var array
     */
    private $translations = [];

    /**
     * @var bool
     */
    private $cacheFile = false;

    /**
     * @var array
     */
    private $stylesUrl = [];

    /**
     * @var array
     */
    private $styleInline = [];

    /**
     * @var array
     */
    private $scriptsUrl = [];

    /**
     * @var array
     */
    private $scriptsInline = [];

    /**
     * @var array
     */
    private $parseParams = ['category'];

    private $viewTemplate;

    /**
     * @var array
     */
    private $classesRegexp = [];

    private $filesRegexp = '/^[A-Za-z\d]+\.php$/';

    private $preloadDirClasses = [];

    /**
     * DocsBuilder constructor.
     * @param $classesRegexp
     */
    public function __construct()
    {
        I18N::setDocBuilderInstance($this);
        $this->cacheFile = __DIR__.'/cache/classes.json';
        $this->viewTemplate = __DIR__.'/views/main.php';
        $this->addStyle(
            new ResourceDTO(
                'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css',
                ResourceDTO::TYPE_URL
            )
        );
        $this->addScript(new ResourceDTO('https://code.jquery.com/jquery-3.4.1.min.js', ResourceDTO::TYPE_URL));
        $this->addScript(
            new ResourceDTO(
                'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js',
                ResourceDTO::TYPE_URL
            )
        );
        $this->addStyle(new ResourceDTO(__DIR__.'/resources/style.css', ResourceDTO::TYPE_FILE));
        $this->addStyle(new ResourceDTO(__DIR__.'/resources/script.js', ResourceDTO::TYPE_FILE));
        $this->cacheResetProcess();
        $this->searchQueryProcess();
    }

    public function addClassRegexp($regexp)
    {
        $this->classesRegexp[] = $regexp;

        return $this;
    }

    public function addPreloadClassesDir($dir)
    {
        $this->preloadDirClasses[] = $dir;

        return $this;
    }

    /**
     * @param ResourceDTO $resourceDTO
     * @return DocsBuilder
     */
    public function addStyle(ResourceDTO $resourceDTO)
    {
        if ($resourceDTO->type === ResourceDTO::TYPE_FILE) {
            $this->styleInline[] = file_get_contents($resourceDTO->file);
        } elseif ($resourceDTO->type === ResourceDTO::TYPE_URL) {
            $this->stylesUrl[] = $resourceDTO->file;
        }

        return $this;
    }

    /**
     * @param ResourceDTO $resourceDTO
     * @return DocsBuilder
     */
    public function addScript(ResourceDTO $resourceDTO)
    {
        if ($resourceDTO->type === ResourceDTO::TYPE_FILE) {
            $this->scriptsInline[] = file_get_contents($resourceDTO->file);
        } elseif ($resourceDTO->type === ResourceDTO::TYPE_URL) {
            $this->scriptsUrl[] = $resourceDTO->file;
        }

        return $this;
    }

    /**
     * @return false|string
     * @throws Exception
     */
    public function buildTemplate()
    {
        $data['classesData'] = $this->afterBuildData($this->buildData());
        $data['stylesInline'] = $this->styleInline;
        $data['stylesUrl'] = $this->stylesUrl;
        $data['scriptsUrl'] = $this->scriptsUrl;
        $data['scriptsInline'] = $this->scriptsInline;
        extract($data);
        ob_start();
        ob_implicit_flush(true);
        require_once $this->viewTemplate;
        $content = ob_get_clean();

        return $content;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function afterBuildData($data)
    {
        return $data;
    }

    /**
     *
     */
    protected function searchQueryProcess() {
        if (isset($_GET['query'])) {
            $this->setSearchQuery($_GET['query']);
            // TODO implement search results
        }
    }

    /**
     * Process cache reset query from user
     */
    protected function cacheResetProcess()
    {
        if ($_POST && isset($_POST['resetcache'])) {
            $this->resetCache();
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    private function buildData()
    {
        $data = $this->getCache();

        if (!$data) {
            if ($this->getPreloadDirClasses()) {
                foreach ($this->preloadDirClasses as $preloadDirClass) {
                    $this->loadClassphp($preloadDirClass);
                }
            }
            $classes = get_declared_classes();

            foreach ($classes as $class) {
                if ($this->isMatchToRegexps($class)) {
                    $reader = new Reader($class);
                    $data[$class] = [
                        'class' => $class,
                    ];
                    foreach ($this->parseParams as $param) {
                        $tempParamVaule = $reader->getParameter($param);
                        $data[$class][$param] = $tempParamVaule;
                    }
                    $methods = get_class_methods($class);
                    foreach ($methods as $method) {
                        $reader = new Reader($class, $method);
                        foreach ($this->parseParams as $param) {
                            $tempParamVaule = $reader->getParameter($param);
                            if ($tempParamVaule) {
                                $data[$class]['methods'][$method][$param] = $tempParamVaule;
                            }
                        }
                    }
                }
            }
            $this->saveCache($data);
        }

        return $data;
    }

    /**
     * @param array $data
     */
    private function saveCache($data = [])
    {
        $this->resetCache();
        file_put_contents($this->cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return array|mixed
     */
    private function getCache()
    {
        $result = [];

        if (is_file($this->cacheFile)) {
            $result = json_decode(file_get_contents($this->cacheFile), true);
        }

        return $result;
    }

    /**
     *
     */
    private function resetCache()
    {
        if (is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    /**
     * @param $class
     * @return bool
     */
    private function isMatchToRegexps($class)
    {
        foreach ($this->classesRegexp as $regexp) {
            if (preg_match($regexp, $class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $param
     * @return $this
     */
    public function addParseParam($param)
    {
        $this->parseParams[] = $param;

        return $this;
    }

    /**
     * @return bool
     */
    public function getPreloadDirClasses()
    {
        return $this->preloadDirClasses;
    }

    /**
     * @param bool $preloadDirClasses
     * @return DocsBuilder
     */
    public function setPreloadDirClasses($preloadDirClasses): DocsBuilder
    {
        $this->preloadDirClasses = $preloadDirClasses;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilesRegexp(): string
    {
        return $this->filesRegexp;
    }

    /**
     * @param string $filesRegexp
     * @return DocsBuilder
     */
    public function setFilesRegexp(string $filesRegexp): DocsBuilder
    {
        $this->filesRegexp = $filesRegexp;

        return $this;
    }

    /**
     * @return array
     */
    public function getParseParams(): array
    {
        return $this->parseParams;
    }

    /**
     * @param array $parseParams
     * @return DocsBuilder
     */
    public function setParseParams(array $parseParams): DocsBuilder
    {
        $this->parseParams = $parseParams;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewTemplate(): string
    {
        return $this->viewTemplate;
    }

    /**
     * @param string $viewTemplate
     * @return DocsBuilder
     */
    public function setViewTemplate(string $viewTemplate): DocsBuilder
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassesRegexp(): string
    {
        return $this->classesRegexp;
    }

    /**
     * @param string $classesRegexp
     * @return DocsBuilder
     */
    public function setClassesRegexp(string $classesRegexp): DocsBuilder
    {
        $this->classesRegexp = $classesRegexp;

        return $this;
    }

    /**
     * @param $directory
     */
    private function loadClassphp($directory)
    {
        if (is_dir($directory)) {
            $scan = scandir($directory);
            unset($scan[0], $scan[1]); //unset . and ..
            foreach ($scan as $file) {
                if (is_dir($directory."/".$file)) {
                    $this->loadClassphp($directory."/".$file);
                } else {
                    // Классы с большой буквы
                    if (preg_match($this->getFilesRegexp(), $file )) {
                        include_once($directory."/".$file);
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return DocsBuilder
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;

        if (is_file(__DIR__."/i18n/{$this->language}.php")) {
            $this->translations = require_once __DIR__."/i18n/{$this->language}.php";
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @param array $translations
     * @return DocsBuilder
     */
    public function setTranslations(array $translations)
    {
        $this->translations = $translations;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     * @param bool $searchQuery
     * @return DocsBuilder
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     * @param bool $cacheFile
     * @return DocsBuilder
     */
    public function setCacheFile(bool $cacheFile)
    {
        $this->cacheFile = $cacheFile;

        return $this;
    }
}