<?php

namespace ItForFree\SimpleAsset;

use ItForFree\rusphp\File\Path;
use ItForFree\rusphp\PHP\Str\StrCommon;

/**
 * Менеджер ассетов (в т.ч. управляет зависимостями)
 * Используется, в частности для непостредственого отображения CSS и JS
 * @see https://github.com/it-for-free/php-simple-assets
 */
class SimpleAssetManager
{
    /**
     * @var string ОСНОВНОЙ парамерт конфигурации -- указывающий куда, относительно корня публиковать ассеты 
     */
    public static $assetsPath = 'assets/';
    
    
    /**
     * Массив объектов-ассетов, которые нужно будет использовать на странице 
     * (на которых был вызван метод ->add())
     * 
     * @var \ItForFree\SimpleMVC\components\SimpleAsset\SimpleAsset[] массив ассетов
     */
    protected static $assets = array();
    
    /**
     *
     * @var type 
     */
    protected static $sortedAssets = array();
    
    /**
     * Массив полных имен уже зарегистрированных классов/ассетов
     * 
     * @var array of string 
     */
    protected static $assetsNames;

    /**
     * Добавит ассет в глобальный список (зарегистрирует его)
     * 
     * @param \ItForFree\SimpleMVC\components\SimpleAsset\SimpleAsset $SimpleAssetObject
     */
    public static function addAsset($SimpleAssetObject)
    {
        static::checkBasePublishingDirectoryExists();
        static::addRequirements($SimpleAssetObject); 
        
        if (empty(static::$assetsNames) 
           || !in_array(get_class($SimpleAssetObject), static::$assetsNames)) {
            static::$assetsNames[] = get_class($SimpleAssetObject);
            static::$assets[get_class($SimpleAssetObject)] = $SimpleAssetObject; 
        }
        $SimpleAssetObject->publish();
    }
    
    /**
     * Добавляем (регистрируем) ассеты, от которых зависит данный $SimpleAssetObject
     * 
     * @param \ItForFree\SimpleMVC\components\SimpleAsset\SimpleAsset $SimpleAssetObject
     */
    protected static function addRequirements($SimpleAssetObject)
    {
        if (!empty($SimpleAssetObject->needs)) {
            foreach ($SimpleAssetObject->needs as $assetName){
                $assetName::add();
            }
        }
    }
    
    /**
     * Распечатает JS
     */
    public static function printJs()
    {
        echo static::getJsHtml();
    }
    
    
    /**
     * Распечатает CSS
     */
    public static function printCss()
    {
        echo static::getCssHtml();
    }
  
    /**
     * Вернёт HTML код для JS
     * @return string
     */
    public static function getJsHtml()
    {
        $html = '';
        foreach (static::$assets as $Asset) {
            foreach ($Asset->publishedPaths['js'] as $filePath) {
                if (!static::isSourceMapFile($filePath)) {
                    $html .= "<script type=\"text/javascript\" src=\""
                        . Path::getWithoutDocumentRoot($filePath, true) ."\"></script>\n";
                }
            }
        }
        return $html;
    }
    
    protected static function isSourceMapFile($path)
    {
        return StrCommon::isEndWith($path, '.map');
    }
    
    /**
     *  Вернёт HTML код для Сss
     * @return string
     */
    public static function getCssHtml()
    {
        $html = '';
        foreach (static::$assets as $Asset) {
            foreach ($Asset->publishedPaths['css'] as $filePath) {
                $html .= '<link rel="stylesheet" type="text/css" href="'
                        . Path::getWithoutDocumentRoot($filePath, true) ."\">\n";
            }
        }
        return $html;
    }

    public static function getPublishBasePath()
    {
        return Path::addEndSlash(
            Path::addToDocumentRoot(static::$assetsPath)
        );
    }
    
    /**
     * Проверит, что базовая для публикации директория в корне сайта 
     * (ниже корневой папки) существует 
     * @throws \Exception
     */
    protected static function checkBasePublishingDirectoryExists()
    {
        if (!is_dir(static::getPublishBasePath())) {
            throw new \Exception("Base asset dir " . static::$assetsPath . " not exists! ");
        }
    }
}
