# php-simple-assets (english,russian doc)
publish assets (js, css etc) from vendor, support dependencies
(inspired by yii2 assets)

## Установка (Install)

Установка с помощью composer:

```
composer require it-for-free/php-simple-assets:~v0.0.1
```
(install via composer).

## Использование (Usage)

`it-for-free/php-simple-assets` предоставляет два класса (`it-for-free/php-simple-assets` package supplies two classes):
* `SimpleAssetManager` -- управляет зависимостями ассетов друг от друга и выводит подключенные файлы в html шаблон
   (manage assets dependencies and print added files into html template)
* `SimpleAsset`   -- базовый класс для описания ассетов (base class for asset definition)

### Настройка SimpleAssetManager (ассета) (Configure Asset Manager)

Во время инициаллизации приложения (или до использования других возможностей данного пакета) 
установите путь к папке асстов (относительно корня сайта):
(set base asset publish path relative to document root during your app init, or before any different usage of this package)
```php
ItForFree\SimpleAsset\SimpleAssetManager::$assetsPath = 'myassets/'; // default 'assets/'
```
-- эта директория должна быть доступной для записи
(`$assetsPath` dir should be writable)

### Создание пакета расурсов (ассета) (Asset declaration)

Определите пакет унаследовавшись от `SimpleAsset`
(define yoor new assets with js and html paths):

```php

namespace application\models;
use ItForFree\SimpleAsset\SimpleAsset;

/**
 *  TestAsset -- пример описания пакета ресурсов (ассета)
 *
 * @author vedro-compota
 */
class BaseAsset extends SimpleAsset
{
    public $basePath = 'JS/'; // from doc root
    public $js = [
        'myjs/basejs.js' // relative from $basePath path
    ];
    
    public $css = [
        'myjs/css/basecss.css' // relative from $basePath path
    ];
}
```

Также можно указать, пакет с зависимостью от другого ассета, для этого заполните свойство-массив 
`$needs` полными именами классов ассетов, от которых зависит данный
(you can define depenedcies by settings `$needs` array property):
```php
use ItForFree\SimpleAsset\SimpleAsset;
use application\models\BaseAsset;

/**
 *  TestAsset -- пример описания пакета ресурсов (ассета)
 *
 * @author vedro-compota
 */
class TestAsset extends SimpleAsset
{
    public $basePath = 'JS/';
    public $js = [
        'myjs/test1.js',
        'myjs/test2222.js'
    ];
    
    public $css = [
        'myjs/css/my.css'
    ];
    
    public $needs = [BaseAsset::class];
}
```

Далее в любом месте кода, например в представлении  зарегистрируем ассет (then in any place in your code register any asset you 
need on page by call `::add()`):

```html
<?php
use application\models\TestAsset;
use ItForFree\SimpleAsset\SimpleAssetManager;

TestAsset::add();
?>

<div class="row">
    <div class="col ">
      <p class="lead"> Тестируем... </p>
      Js:
      <pre><?php SimpleAssetManager::printJs()  ?></pre>
      Css:
      <pre><?php SimpleAssetManager::printCss()  ?></pre>
    </div>
</div>

```
-- Как видно, `SimpleAssetManager` может выводить все зарегистрированные JS и CSS в нужном порядке.
(as you can see `SimpleAssetManager` can print JS or Css like HTML resource tags)