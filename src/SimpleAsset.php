<?php

namespace ItForFree\SimpleMVC\components\SimpleAsset;

use ItForFree\SimpleMVC\components\SimpleAsset\SimpleAssetManager;
use ItForFree\rusphp\File\Path;
use ItForFree\rusphp\PHP\Hash\LengthHash;
use ItForFree\rusphp\File\Directory\Directory;

/**
 * Базовый класс
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
    */
   public static function add()
   {
       $name = get_called_class();
       $Asset = new $name;
       $Asset->basePath = Path::addToDocumentRoot($Asset->basePath); // делаем относительный путь абсолютным
       if (!is_dir($Asset->basePath)) {
            throw new \Exception("Source asset dir {$Asset->basePath} not exists for " . get_class($Asset) ."! ");
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
//       pdie($baseAssetPublishPath);
       

       Directory::createRecIfNotExists($baseAssetPublishPath, 0777);
       
       $lastChangeFileTimestamp = $this->getLastChangeFileTimestamp();
       
//       pdie($lastChangeFileTimestamp); 
       $baseAssetTimePath = $baseAssetPublishPath 
            . DIRECTORY_SEPARATOR . $lastChangeFileTimestamp;
       
          
       if (is_dir($baseAssetTimePath)) {
           $this->setPublishPaths($baseAssetTimePath . DIRECTORY_SEPARATOR);
           return; // если ничего не изменилось
       } else {
           Directory::clear($baseAssetPublishPath); // полностью очищаем родительскую директорию
           $this->copyToAssetsDir($baseAssetTimePath . DIRECTORY_SEPARATOR);
       }
   }
   
   /**
    * Копирует файлы в папку, которая предполагается публичной
    * 
    * @param string $basePublishPath Путь к базовой папке публикации ассета (последний сегмент -- таймстемп)
    */
   protected function copyToAssetsDir($basePublishPath)
   {
        $assetSourcePath = $this->basePath;
        
        $pubFolderPath = $basePublishPath . 'js/' ;
        Directory::createRecIfNotExists($pubFolderPath, 0777);
        foreach ($this->js as $filePath) {         
            $fullPubPath = $pubFolderPath . Path::getFileName($filePath);
            $fullSourcePath = $assetSourcePath . DIRECTORY_SEPARATOR . $filePath;
            copy($fullSourcePath, $fullPubPath);
            $this->addToPublishedPaths($fullPubPath, 'js');
        }

        $pubFolderPath = $basePublishPath . 'css/' ;
        Directory::createRecIfNotExists($pubFolderPath, 0777);        
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
        $pubFolderPath = $basePublishPath . 'js/' ;
        foreach ($this->js as $filePath) {         
            $fullPubPath = $pubFolderPath . Path::getFileName($filePath);
            $this->addToPublishedPaths($fullPubPath, 'js');
        }

        $pubFolderPath = $basePublishPath . 'css/' ;       
        foreach ($this->css as $filePath) {
            $fullPubPath = $pubFolderPath . Path::getFileName($filePath);
            $this->addToPublishedPaths($fullPubPath, 'css');
        }
   }
   
   
   
   /**
    * Добавит данные к массиву путей публикации данного ассета
    * @param string $path
    * @param string $type  тип, например 'js'
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

        foreach ($this->js as $filePath) {
            $fullPath = $assetSourcePath . DIRECTORY_SEPARATOR . $filePath;
//            ppre($fullPath);
            $currentLastTime = filemtime($fullPath);
            
            if ($lasttime < $currentLastTime) {
                $lasttime = $currentLastTime;
            }
        }
        
        foreach ($this->css as $filePath) {
            $fullPath = $assetSourcePath . DIRECTORY_SEPARATOR . $filePath;
            $currentLastTime = filemtime($fullPath);
            
            if ($lasttime < $currentLastTime) {
                $lasttime = $currentLastTime;
            }
        }
        
//        pdie($lasttime);
        
        return $lasttime; 
   } 
}
