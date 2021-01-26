<?php

namespace ItForFree\SimpleAsset;

use ItForFree\SimpleAsset\SimpleAssetManager;
use ItForFree\rusphp\File\Path;
use ItForFree\rusphp\PHP\Hash\LengthHash;
use ItForFree\rusphp\File\Directory\Directory;

/**
 * Базовый класс дял описания вашего ассета -- от него нужно отнаследоваться
 * чтобы определить свой ассет
 * @see https://github.com/it-for-free/php-simple-assets
 */
class SimpleAsset
{
    /**
     * Путь относительно корня сайта к базовой директории
     * @var string
     */
    public $basePath = 'test-source-path/';

    /**
     * @var string[] Массив путей к JS файлам, относительно  $this->basePath
     */
    public $js = array();

    /**
     * @var string[] Массив путей к CSS файлам, относительно  $this->basePath
     */
    public $css = array();

    /**
     * @var string[] массив строк -- полных имен классов ассетов, от которых зависит данный ассет
     */
    public $needs = array();

    /**
     * Массив подмассивов для путей опубликованных файлов
     *
     * @var array[]
     */
    public $publishedPaths = array(
        'js' => array(),
        'css' => array(),
    );

    /**
     * Добавляет ресурсы (информацию о них) данного пакета к глобальному списку, который
     * далее можно будет распечатать в шаблоне с помощью
     * класса SimpleAssetManager
     * @throws \Exception
     */
    public static function add()
    {
        $Asset = new static();

        $Asset->basePath = Path::addToDocumentRoot($Asset->basePath); // делаем относительный путь абсолютным

        if (!is_dir($Asset->basePath)) {
            throw new \Exception("Source asset dir {$Asset->basePath} not exists for " . get_class($Asset) . "! ");
        }
        SimpleAssetManager::addAsset($Asset);
    }

    /**
     * Основной метод для публикации ресурсов ассета
     * @return null
     */
    public function publish()
    {
        $baseAssetPublishPath = SimpleAssetManager::getPublishBasePath() . LengthHash::md5(static::class, 10);


        Directory::createRecIfNotExists($baseAssetPublishPath, 0777);

        $lastChangeFileTimestamp = $this->getLastChangeFileTimestamp();

        $baseAssetTimePath = $baseAssetPublishPath
            . DIRECTORY_SEPARATOR . $lastChangeFileTimestamp;

        if (is_dir($baseAssetTimePath)) {
            $this->setPublishPaths($baseAssetTimePath . DIRECTORY_SEPARATOR);
            return; // если ничего не изменилось
        }

        // Если изменили, добавили или удалили файл ассета, то удаляем все ассеты и загружаем актуальные
        Directory::clear($baseAssetPublishPath); // полностью очищаем родительскую директорию
        $this->copyToAssetsDir($baseAssetTimePath . DIRECTORY_SEPARATOR);
    }

    /**
     * Копирует файлы в папку, которая предполагается публичной
     *
     * @param string $basePublishPath Путь к базовой папке публикации ассета (последний сегмент -- таймстемп)
     */
    protected function copyToAssetsDir($basePublishPath)
    {
        $assetSourcePath = $this->basePath;
        $pubFolderPath = $basePublishPath . 'js/';
        if (!empty($this->js)) {
            Directory::createRecIfNotExists($pubFolderPath, 0777);
        }
        foreach ($this->js as $filePath) {
            $fullPubPath = $pubFolderPath . Path::getFileName($filePath);
            $fullSourcePath = $assetSourcePath . DIRECTORY_SEPARATOR . $filePath;
            copy($fullSourcePath, $fullPubPath);
            $this->addToPublishedPaths($fullPubPath, 'js');
        }

        $pubFolderPath = $basePublishPath . 'css/';
        if (!empty($this->css)) {
            Directory::createRecIfNotExists($pubFolderPath, 0777);
        }
        foreach ($this->css as $filePath) {
            $fullPubPath = $pubFolderPath . Path::getFileName($filePath);
            $fullSourcePath = $assetSourcePath . DIRECTORY_SEPARATOR . $filePath;
            copy($fullSourcePath, $fullPubPath);
            $this->addToPublishedPaths($fullPubPath, 'css');
        }
    }

    /**
     * Заполнит массив публикационных путей (хотя файлы реально копироваться не будут)
     *
     * @param string $basePublishPath Путь к базовой папке публикации ассета (последний сегмент -- таймстемп)
     */
    protected function setPublishPaths($basePublishPath)
    {
        $pubFolderPath = $basePublishPath . 'js/';
        foreach ($this->js as $filePath) {
            $fullPubPath = $pubFolderPath . Path::getFileName($filePath);
            $this->addToPublishedPaths($fullPubPath, 'js');
        }

        $pubFolderPath = $basePublishPath . 'css/';
        foreach ($this->css as $filePath) {
            $fullPubPath = $pubFolderPath . Path::getFileName($filePath);
            $this->addToPublishedPaths($fullPubPath, 'css');
        }
    }


    /**
     * Добавит данные к массиву путей публикации данного ассета
     * @param string $path
     * @param string $type тип, например 'js'
     */
    protected function addToPublishedPaths($path, $type)
    {
        $this->publishedPaths[$type][] = $path;
    }

    /**
     * Время (таймстемп) последнего изменения файла -- самого последнего,
     * из тех, что перечислены в массиве конкретного ассета
     *
     * @return int
     */
    protected function getLastChangeFileTimestamp()
    {
        $assetSourcePath = $this->basePath;
        $lasttime = 0;
        $filesNames = '';
        foreach ($this->js as $filePath) {
            $fullPath = $assetSourcePath . DIRECTORY_SEPARATOR . $filePath;
            $currentLastTime = filemtime($fullPath);
            $filesNames .= $filePath; //собираем имена ассетов в строку

            if ($lasttime < $currentLastTime) {
                $lasttime = $currentLastTime;
            }
        }

        foreach ($this->css as $filePath) {
            $fullPath = $assetSourcePath . DIRECTORY_SEPARATOR . $filePath;
            $currentLastTime = filemtime($fullPath);
            $filesNames .= $filePath; //собираем имена ассетов в строку

            if ($lasttime < $currentLastTime) {
                $lasttime = $currentLastTime;
            }
        }

        $filesNamesHash = LengthHash::md5($filesNames, 6);

        return $lasttime . $filesNamesHash;
    }
}
