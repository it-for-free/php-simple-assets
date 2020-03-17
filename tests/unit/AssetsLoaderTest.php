<?php

use Codeception\Test\Unit;
use ItForFree\rusphp\PHP\Hash\LengthHash;
use Test\NewTestAsset;
use Test\TestAsset;

class AssetsLoaderTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;
    protected $basePath;
    protected $targetPath;

    protected function _before()
    {

        $this->basePath = codecept_data_dir();
        $this->targetPath = codecept_output_dir() .'assets/';
        if (!file_exists($this->targetPath)) {
            mkdir($this->targetPath);
        }

    }

    protected function _after()
    {
        \ItForFree\rusphp\File\Directory\Directory::clear($this->targetPath, true);
    }

    public function testAssetsAdding()
    {
        // Устанавливаем директорию назначения Ассетов
        $this->make(ItForFree\SimpleAsset\SimpleAssetManager::class, [
            'assetsPath' => $this->targetPath
        ]);

        // Создаем основной класс, указывая, откуда брать данные и сами данные
        $asset = $this->make(ItForFree\SimpleAsset\SimpleAsset::class, [
            'basePath' => $this->basePath,
            'js' => [
                'myassets/test1.js',
            ],
            'css' => [
                'myassets/css/my.css'
            ],

        ]);

        // Так как основной класс уже создан, нужны в вызове SimpleAsset::add нет
        // сразу запускаем проверку необходимости загрузки данных
        $asset->publish();

        // Проверяем, что данные загружены
        $this->tester->seeFileFound('test1.js', $this->targetPath);
        $this->tester->seeFileFound('my.css', $this->targetPath);

        // Меняем настройки основного класса, добавляя новый файл в список загружаемых
        $asset = $this->make(ItForFree\SimpleAsset\SimpleAsset::class, [
            'basePath' => $this->basePath,
            'js' => [
                'myassets/test1.js',
                'myassets/test2222.js'
            ],
            'css' => [
                'myassets/css/my.css'
            ],
        ]);

        // Проверяем необходимость загрузки
        $asset->publish();

        // Проверяем, что данные загружены
        $this->tester->seeFileFound('test1.js', $this->targetPath);
        $this->tester->seeFileFound('my.css', $this->targetPath);
        $this->tester->seeFileFound('test2222.js', $this->targetPath);

        //Сравниваем дату создания добавленного файла и у уже существующего
        $time1 = filemtime($this->basePath . $asset->js[0]);
        $time2 = filemtime($this->basePath . $asset->js[1]);
        $this->tester->assertGreaterOrEquals($time2, $time1, 'Файл 2 создан раньше файла 1');
    }
}