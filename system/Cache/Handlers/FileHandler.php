<?php

declare(strict_types=1);

namespace CodeIgniter\Cache\Handlers;

use CodeIgniter\Cache\Exceptions\CacheException;
use CodeIgniter\I18n\Time;
use Config\Cache;
use Throwable;
use function is_writable;
use function is_dir;
use function mkdir;
use function is_file;
use function unlink;
use function write_file;
use function get_dir_file_info;
use function delete_files;

/**
 * File system cache handler
 *
 * @see \CodeIgniter\Cache\Handlers\FileHandlerTest
 */
class FileHandler extends BaseHandler
{
    /**
     * Maximum key length.
     */
    public const MAX_KEY_LENGTH = 255;

    /**
     * Where to store cached files on the disk.
     *
     * @var string
     */
    protected $path;

    /**
     * Mode for the stored files.
     * Must be chmod-safe (octal).
     *
     * @var int
     */
    protected $mode;

    /**
     * Note: Use `CacheFactory::getHandler()` to instantiate.
     *
     * @throws CacheException
     */
    public function __construct(Cache $config)
    {
        $options = [
            ...['storePath' => WRITEPATH . 'cache', 'mode' => 0640],
            ...$config->file,
        ];

        $this->path = rtrim($options['storePath'], '\\/') . '/';
        $this->mode = $options['mode'];
        $this->prefix = $config->prefix;

        // Check if cache directory is writable
        if (! is_really_writable($this->path)) {
            if (! is_dir($this->path)) {
                try {
                    mkdir($this->path, 0777, true);
                } catch (Throwable $e) {
                    throw CacheException::forUnableToWrite($this->path, $e);
                }
            }

            if (! is_really_writable($this->path)) {
                throw CacheException::forUnableToWrite($this->path);
            }
        }

        helper('filesystem');
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        // Initialization logic, if required
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        $key  = static::validateKey($key, $this->prefix);
        $data = $this->getItem($key);

        return is_array($data) ? $data['data'] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $key, $value, int $ttl = 60)
    {
        $key = static::validateKey($key, $this->prefix);

        $contents = [
            'time' => Time::now()->getTimestamp(),
            'ttl'  => $ttl,
            'data' => $value,
        ];

        $filePath = $this->path . $key;

        if (write_file($filePath, serialize($contents))) {
            try {
                chmod($filePath, $this->mode);
            } catch (Throwable $e) {
                log_message('debug', 'Failed to set mode on cache file: ' . $e->getMessage());
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key)
    {
        $key = static::validateKey($key, $this->prefix);

        return is_file($this->path . $key) && unlink($this->path . $key);
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function deleteMatching(string $pattern)
    {
        $deleted = 0;

        foreach (glob($this->path . $pattern, GLOB_NOSORT) as $filename) {
            if (is_file($filename) && unlink($filename)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * {@inheritDoc}
     */
    public function increment(string $key, int $offset = 1)
    {
        $prefixedKey = static::validateKey($key, $this->prefix);
        $tmp         = $this->getItem($prefixedKey);

        if ($tmp === false) {
            $tmp = ['data' => 0, 'ttl' => 60];
        }

        ['data' => $value, 'ttl' => $ttl] = $tmp;

        if (! is_int($value)) {
            return false;
        }

        $value += $offset;

        return $this->save($key, $value, $ttl) ? $value : false;
    }

    /**
     * {@inheritDoc}
     */
    public function decrement(string $key, int $offset = 1)
    {
        return $this->increment($key, -$offset);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        return delete_files($this->path, false, true);
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheInfo()
    {
        return get_dir_file_info($this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaData(string $key)
    {
        $key = static::validateKey($key, $this->prefix);

        if (false === $data = $this->getItem($key)) {
            return false; // @TODO This will return null in a future release
        }

        return [
            'expire' => $data['ttl'] > 0 ? $data['time'] + $data['ttl'] : null,
            'mtime'  => filemtime($this->path . $key),
            'data'   => $data['data'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function isSupported(): bool
    {
        return is_writable($this->path);
    }

    /**
     * Does the heavy lifting of actually retrieving the file and verifying its age.
     *
     * @return array{data: mixed, ttl: int, time: int}|false
     */
    protected function getItem(string $filename)
    {
        $filePath = $this->path . $filename;

        if (! is_file($filePath)) {
            return false;
        }

        $content = @file_get_contents($filePath);

        if ($content === false) {
            return false;
        }

        try {
            $data = unserialize($content);
        } catch (Throwable $e) {
            return false;
        }

        if (! is_array($data)) {
            return false;
        }

        if (! isset($data['ttl']) || ! is_int($data['ttl'])) {
            return false;
        }

        if (! isset($data['time']) || ! is_int($data['time'])) {
            return false;
        }

        if ($data['ttl'] > 0 && Time::now()->getTimestamp() > $data['time'] + $data['ttl']) {
            unlink($filePath);
            return false;
        }

        return $data;
    }
}
