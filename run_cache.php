<?php
namespace App;

use App\Cache\Cache;
use App\Cache\Drivers\FileCache;
use App\Cache\Drivers\MemcacheCache;
use App\Cache\Drivers\StaticCache;

require_once __DIR__ . '/vendor/autoload.php';


//эталонное обращение к функционалу

$cache1 = new Cache(/*config if you need*/);
/*postconfig or any preparing if you need*/

#usage
$cache1->set('key1', 'val1'); #

echo $cache1->get('key1') . "\n"; # val1

//Система должна уметь реконфигурироваться
//- по приоритету использования кеша

$cache2 = new Cache(/*config if you need*/);
$cache2->clearDrivers();
$cache2->addDriver(new FileCache());
$cache2->addDriver(new StaticCache());

#usage
$cache2->set('key2', 'val2'); #

echo $cache2->get('key2') . "\n"; # val2

//- по количеству носителей для кеш слоя, например, мы сможем добавить хранение в memcache

$memcached = new \Memcache();
$is_connect = $memcached->addServer('localhost', 11211);
if ( !$is_connect ) {
    echo "Connection to memcached failed";
    return;
}

$cache3 = new Cache(/*config if you need*/);
$memcacheCache = new MemcacheCache($memcached);
$cache2->clearDrivers();
$cache3->addDriver($memcacheCache);

#usage
$cache3->set('key3', 'val3'); #

echo $cache3->get('key3') . "\n"; # val3

$memcached->close();