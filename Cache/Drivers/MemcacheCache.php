<?php


namespace App\Cache\Drivers;

use \Memcache;

class MemcacheCache implements CacheItemInterface {
    /**
     * @var Memcache
     */
    protected $Memcache;
    
    /**
     * MemcacheCache constructor.
     * @param Memcache $Memcache
     */
    public function __construct (Memcache $Memcache) {
        $this->Memcache = $Memcache;
        $this->Memcache->setCompressThreshold(0, 1);
    }

    /**
     * Возвращает значение кэша
     *
     * @param string $key
     * @return mixed
     */
    public function get (string $key) {
        $data = unserialize($this->Memcache->get($key));
        if (!empty($data['remove']) && $data['remove'] < time()) {
            return false;
        }
        return $data['data'] ?? null;
    }

    /**
     * Записывает кэш в файл
     *
     * @param string $key
     * @param $data
     * @param int|null $interval
     * @return bool
     */
    public function set (string $key, $data, int $interval = null) : bool {
        return $this->Memcache->set($key, gzencode($data, 9), MEMCACHE_COMPRESSED, $interval) == true;
    }
    
    /**
     * Удаление кеша
     *
     * @param string|null $key
     * @return bool
     */
    public function delete (string $key = null) : bool {
        return $this->Memcache->set($key, false) == true;
    }

    /**
     * Очистка кеша
     *
     * @return bool
     */
    public function clear () : bool {
        return $this->Memcache->flush() == true;
    }
}