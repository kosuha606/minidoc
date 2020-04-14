<?php

namespace kosuha606\Minidoc;

class I18N
{
    /** @var DocsBuilder */
    private static $docBuilderInstance;

    /**
     * @param DocsBuilder $docBuilderInstance
     */
    public static function setDocBuilderInstance(DocsBuilder $docBuilderInstance)
    {
        self::$docBuilderInstance = $docBuilderInstance;
    }

    public static function translate($message = '')
    {
        if (isset(self::$docBuilderInstance->getTranslations()[$message])) {
            return self::$docBuilderInstance->getTranslations()[$message];
        }

        return $message;
    }

    public static function getTranslationsJson()
    {
        return json_encode(self::$docBuilderInstance->getTranslations(), JSON_UNESCAPED_UNICODE);
    }
}