<?php 
class AssetsLoaderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $basePath;
    protected $targetPath;

    protected function _before()
    {
        $this->basePath = codecept_data_dir();
        $this->targetPath = codecept_output_dir() .'/assets/';
        if (!file_exists($this->targetPath)) {
            mkdir($this->targetPath);
        }

    }

    protected function _after()
    {
        \ItForFree\rusphp\File\Directory\Directory::clear($this->targetPath);
    }

    // tests
/*    public function testSomeFeature()
    {
        $assetsPath = __DIR__.'/js_test/';
        if (!file_exists($assetsPath)) {
            mkdir($assetsPath);
        }

        $files = ['file2.js', 'file1.js'];

        foreach ($files as $file) {
            $newFile = fopen($assetsPath . $file, 'wb');
            fwrite($newFile, $file);
//            sleep(2);
        }
        $asset = $this->make(ItForFree\SimpleAsset\SimpleAsset::class, [
            'basePath' => $assetsPath,
            'js' => [$files[1]],
            'copyToAssetsDir' => Codeception\Stub\Expected::exactly(1)
        ]);

        $simpleAssetManager = $this->make(ItForFree\SimpleAsset\SimpleAssetManager::class, [
            'assetsPath' => $assetsPath . '/assets',
        ]);

        $asset->needs = [];
        $asset->publish('');

    }*/

    public function testAssetsAdding()
    {

        \Test\TestAsset::add($this->basePath, $this->targetPath);
        \Test\NewTestAsset::add($this->basePath, $this->targetPath);

    }
}