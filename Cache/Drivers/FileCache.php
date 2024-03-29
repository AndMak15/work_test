<?php

namespace App\Cache\Drivers;

class FileCache implements CacheItemInterface  {
    /**
     * Настройки класса
     *
     * @var array
     */
    protected $configuration = [
        'directory' => __DIR__ . '/Storage'
    ];

    /**
     * FileCache constructor.
     * @param array $configuration
     */
    public function __construct (array $configuration = []) {
        $this->configure($configuration);
    }

    /**
     * Задаёт конфигурацию
     *
     * @param array $configuration
     * @return array
     */
    public function configure (array $configuration = []) : array {
        return $this->configuration = array_merge($this->configuration, $configuration);
    }

    /**
     * Возвращает значение кэша
     *
     * @param string $key
     * @return mixed
     */
    public function get (string $key) {
        $path = $this->getPath($key);
        if ( is_file($path) ) {
            $data = unserialize(file_get_contents($path));
            if ( !empty($data['remove']) && $data['remove'] < time() ) {
                return false;
            }
            return $data['data'] ?? false;
        }
        return null;
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
        $path = $this->getPath($key);
        $dir = dirname($path);
        if ( !is_dir($dir) ) {
            mkdir($dir, 0700, true);
        }
        $data = [
            'created' => time(),
            'remove'  => $interval ? time() + $interval : null,
            'data'    => $data
        ];

        return file_put_contents($path, serialize($data), LOCK_EX) == true;
    }

    /**
     * Удаление кеша
     *
     * @param string $key
     * @return bool
     */
    public function delete (string $key) : bool {
        return $this->deleteRecursive($this->getPath($key)) == true;
    }

    /**
     * Очистка кеша
     *
     * @return bool
     */
    public function clear () : bool {
        if ( !is_dir($this->configuration['directory']) ) {
            return true;
        }
        return $this->deleteRecursive($this->configuration['directory']) == true;
    }

    /**
     * Рекурсивное удаление файлов
     *
     * @param $path
     * @return bool
     */
    protected function deleteRecursive ($path) : bool {
        if ( is_dir($path) ) {
            foreach (scandir($path) as $file) {
                if ( $file != '.' && $file != '..' ) {
                    if ( is_file($file) ) {
                        unlink($file);
                    } else {
                        $this->deleteRecursive($path . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
            $emptyDir = count(glob($path . '*')) ? true : false;
            if ( $emptyDir ) {
                return rmdir($path);
            }
        } elseif ( is_file($path) ) {
            return unlink($path);
        }
        return false;
    }

    /**
     * Возврщает путь до файла
     *
     * @param string $key
     * @return string
     */
    protected function getPath (string $key) : string {
        $path = $this->configuration['directory'] . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $key);
        if ( is_dir($path) || is_file($path) ) {
            return $path;
        } else {
            $dir  = dirname($path);
            $hash = md5(basename($path));
            return $dir . DIRECTORY_SEPARATOR . '.' . $hash[0] . $hash[1] . DIRECTORY_SEPARATOR . '.' . $hash[2] . $hash[3] . DIRECTORY_SEPARATOR . '.' . $hash;
        }
    }
}