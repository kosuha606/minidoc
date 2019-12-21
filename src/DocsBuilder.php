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

    private $preloadDirClasses = [];

    /**
     * DocsBuilder constructor.
     * @param $classesRegexp
     */
    public function __construct()
    {
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
        if ($this->getPreloadDirClasses()) {
            foreach ($this->preloadDirClasses as $preloadDirClass) {
                $this->loadClassphp($preloadDirClass);
            }
        }
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
     * @return array
     * @throws Exception
     */
    private function buildData()
    {
        $classes = get_declared_classes();
        $data = [];

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
            }
        }

        return $data;
    }

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
                    if (preg_match("/^[A-Z]/", $file )) {
                        include_once($directory."/".$file);
                    }
                }
            }
        }
    }
}