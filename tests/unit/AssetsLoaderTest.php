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

    public function testAss()
    {
        $asset = $this->make(ItForFree\SimpleAsset\SimpleAsset::class, [
            'basePath' => $this->basePath,
            'js' => [
                'myassets/test1.js',
            ],
            'css' => [
                'myassets/css/my.css'
            ],

        ]);

        $asset->publish($this->targetPath);

        $this->tester->seeFileFound('test1.js', $this->targetPath);
        $this->tester->seeFileFound('my.css', $this->targetPath);

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

        $asset->publish($this->targetPath);

        $this->tester->seeFileFound('test1.js', $this->targetPath);
        $this->tester->seeFileFound('my.css', $this->targetPath);
        $this->tester->seeFileFound('test2222.js', $this->targetPath);

        $time1 = filemtime($this->basePath . $asset->js[0]);
        $time2 = filemtime($this->basePath . $asset->js[1]);
        $this->tester->assertGreaterOrEquals($time2, $time1, 'Файл 2 создан раньше файла 1');
    }



/*    public function testAssetsAddingNew()
    {
//        $classFile = codecept_root_dir() . 'tests/_support/TestAsset.php';
//        $fileLines = file($classFile);
//        $fileLines[19] = "//        'myassets/test2222.js'\n";
//        file_put_contents($classFile, $fileLines);

//        TestAsset::add($this->basePath, $this->targetPath);
        $TestAsset = new ItForFree\SimpleAsset\SimpleAsset();
        $TestAsset->js = [
            'myassets/test1.js',
//            'myassets/test2222.js'
        ];
        $this->tester->seeFileFound('test1.js', $this->targetPath);

        $this->tester->seeFileFound('my.css', $this->targetPath);


    }
    public function testAssetsAdding()
    {
        $searchPath = $this->targetPath . LengthHash::md5(TestAsset::class, 10);
        $TestAsset = new ItForFree\SimpleAsset\SimpleAsset();
        $TestAsset->js = [
            'myassets/test1.js',
            'myassets/test2222.js'
        ];
//        vdie($TestAsset);
        $TestAsset->add($this->basePath, $this->targetPath);
        $this->tester->seeFileFound('test1.js', $this->targetPath);
        $this->tester->seeFileFound('my.css', $this->targetPath);
        $this->tester->seeFileFound('test2222.js', $this->targetPath);
//        $classFile = codecept_root_dir() . 'tests/_support/TestAsset.php';
//        $fileLines = file($classFile);
//        $fileLines[19] = "        'myassets/test2222.js'\n";
//        file_put_contents($classFile, $fileLines);
    }*/
}