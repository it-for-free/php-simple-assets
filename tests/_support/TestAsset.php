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
class TestAsset extends SimpleAsset
{
    public $js = [
        'myassets/test1.js'
    ];
    
    public $css = [
        'myassets/css/my.css'
    ];
    
}
