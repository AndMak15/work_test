<?php

namespace App\Cache\Drivers;

class StaticCache implements CacheItemInterface {
    private static $storage = [];

    /**
     * Возвращает значение кэша
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key) {
        $storage = self::$storage;

        if ( isset($storage[$key]) ) {
            if ( !empty($storage[$key]['remove']) && $storage[$key]['remove'] < time() ) {
                return false;
            }
            return $storage[$key]['data'] ?? false;
        }
        return null;
    }

    /**
     * Записывает  значение в кэш
     *
     * @param string $key
     * @param $data
     * @param int|null $interval
     * @return bool
     */
    public function set(string $key, $data, int $interval = null) : bool {
        self::$storage[$key] = [
            'created' => time(),
            'remove'  => $interval ? time() + $interval : null,
            'data'    => $data
        ];

        return true;
    }

    /**
     * Удаление кеша
     *
     * @param string $key
     * @return bool
     */
    public function delete (string $key) : bool {
        if ( isset(self::$storage[$key]) ) {
            unset(self::$storage[$key]);
        }
        return true;
    }

    /**
     * Очистка кеша
     *
     * @return bool
     */
    public function clear () : bool {
        self::$storage = [];
        return true;
    }
}