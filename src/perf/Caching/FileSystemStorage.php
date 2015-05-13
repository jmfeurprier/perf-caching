<?php

namespace perf\Caching;

/**
 *
 *
 */
class FileSystemStorage implements Storage
{

    const CACHE_FILE_SUFFIX = '.cache';

    /**
     * Base path where cache files are stored.
     *
     * @var string
     */
    private $basePath;

    /**
     * Constructor.
     *
     * @param string $basePath
     * @return void
     */
    public function __construct($basePath)
    {
        $this->basePath = rtrim((string) $basePath, '/\\') . '/';
    }

    /**
     * Attempts to store provided cache entry into storage.
     *
     * @param CacheEntry $entry
     * @return void
     * @throws \RuntimeException
     */
    public function store(CacheEntry $entry)
    {
        $cacheFilePath = $this->getCacheFilePath($entry->getId());

        $packedContent = serialize($entry->getContent());

        $fileContent = $entry->getCreationTimestamp() . "\n"
                     . ($entry->hasExpirationTimestamp() ? $entry->getExpirationTimestamp() : '-') . "\n"
                     . $packedContent;

        if (false === file_put_contents($cacheFilePath, $fileContent)) {
            throw new \RuntimeException('Failed to store cache entry.');
        }
    }

    /**
     *
     *
     * @param string $id Cache item unique identifier (ex: 123).
     * @return null|CacheEntry
     * @throws \RuntimeException
     */
    public function tryFetch($id)
    {
        $cacheFilePath = $this->getCacheFilePath($id);

        // Cache file missing?
        if (!file_exists($cacheFilePath)) {
            return null;
        }

        $fileContent = file_get_contents($cacheFilePath);

        // File read failure?
        if (false === $fileContent) {
            throw new \RuntimeException('Failed to read cache file.');
        }

        // Extracting timestamps (creation and expiration) and packed content from file
        $exploded = explode("\n", $fileContent, 3);
        if (3 !== count($exploded)) {
            throw new \RuntimeException('Invalid cache file content.');
        }
        list($creationTimestamp, $expirationTimestamp, $packedContent) = $exploded;

        if ('-' === $expirationTimestamp) {
            $expirationTimestamp = null;
        }

        $content = unserialize($packedContent);

        return new CacheEntry($id, $content, $creationTimestamp, $expirationTimestamp);
    }

    /**
     *
     *
     * @param string $id Cache entry unique identifier.
     * @return void
     */
    public function flushById($id)
    {
        $cacheFilePath = $this->getCacheFilePath($id);

        if (!unlink($cacheFilePath)) {
            throw new \RuntimeException("Failed to delete cache file '{$cacheFilePath}'.");
        }
    }

    /**
     * Deletes every cache file.
     *
     * @return void
     * @throws \RuntimeException
     */
    public function flushAll()
    {
        $mask = '*' . self::CACHE_FILE_SUFFIX;

        foreach (glob($this->basePath . $mask) as $cacheFilePath) {
            if (!unlink($cacheFilePath)) {
                throw new \RuntimeException("Failed to delete cache file '{$cacheFilePath}'.");
            }
        }
    }

    /**
     * Returns the cache file path where content can be read / written for the provided cache entry Id.
     *
     * @param string $id cache item unique identifier (ex: "123").
     * @return string cache file path for provided cache group and item pair.
     */
    private function getCacheFilePath($id)
    {
        return $this->basePath . md5($id) . self::CACHE_FILE_SUFFIX;
    }
}
