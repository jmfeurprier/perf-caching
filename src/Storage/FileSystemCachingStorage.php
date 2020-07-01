<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;
use RuntimeException;

class FileSystemCachingStorage implements CachingStorageInterface
{
    const CACHE_FILE_SUFFIX = '.cache';

    /**
     * Base path where cache files are stored.
     */
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim((string) $basePath, '/\\') . '/';
    }

    public function store(CacheEntry $entry): void
    {
        $cacheFilePath = $this->getCacheFilePath($entry->getId());

        $packedContent = serialize($entry->getContent());

        $fileContent = $entry->getCreationTimestamp() . "\n"
                     . ($entry->hasExpirationTimestamp() ? $entry->getExpirationTimestamp() : '-') . "\n"
                     . $packedContent;

        if (false === file_put_contents($cacheFilePath, $fileContent)) {
            throw new RuntimeException('Failed to store cache entry.');
        }
    }

    public function tryFetch(string $id): ?CacheEntry
    {
        $cacheFilePath = $this->getCacheFilePath($id);

        // Cache file missing?
        if (!file_exists($cacheFilePath)) {
            return null;
        }

        $fileContent = file_get_contents($cacheFilePath);

        // File read failure?
        if (false === $fileContent) {
            throw new RuntimeException('Failed to read cache file.');
        }

        // Extracting timestamps (creation and expiration) and packed content from file
        $exploded = explode("\n", $fileContent, 3);
        if (3 !== count($exploded)) {
            throw new RuntimeException('Invalid cache file content.');
        }
        list($creationTimestamp, $expirationTimestamp, $packedContent) = $exploded;

        if ('-' === $expirationTimestamp) {
            $expirationTimestamp = null;
        }

        $content = unserialize($packedContent);

        return new CacheEntry($id, $content, $creationTimestamp, $expirationTimestamp);
    }

    public function flushById(string $id): void
    {
        $cacheFilePath = $this->getCacheFilePath($id);

        if (!unlink($cacheFilePath)) {
            throw new RuntimeException("Failed to delete cache file '{$cacheFilePath}'.");
        }
    }

    public function flushAll(): void
    {
        $mask = '*' . self::CACHE_FILE_SUFFIX;

        foreach (glob($this->basePath . $mask) as $cacheFilePath) {
            if (!unlink($cacheFilePath)) {
                throw new RuntimeException("Failed to delete cache file '{$cacheFilePath}'.");
            }
        }
    }

    /**
     * Returns the cache file path where content can be read / written for the provided cache entry Id.
     *
     * @param string $id cache item unique identifier (ex: "123").
     *
     * @return string Cache file path for provided cache group and item pair.
     */
    private function getCacheFilePath(string $id): string
    {
        return $this->basePath . md5($id) . self::CACHE_FILE_SUFFIX;
    }
}
