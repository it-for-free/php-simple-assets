<?php

/**
 * Временная модель для тестирования
 */
namespace Test;

use ItForFree\SimpleAsset\SimpleAsset;
use Test\BaseAsset;

/**
 *  TestAsset -- пример описания пакета ресурсов (ассета)
 *
 * @author vedro-compota
 */
class NewTestAsset extends SimpleAsset
{
    public $js = [
        'myassets/test1.js',
        'myassets/test2222.js'
    ];
    
    public $css = [
        'myassets/css/my.css'
    ];
    
}
