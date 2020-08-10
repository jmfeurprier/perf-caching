<?php

namespace perf\Caching\Storage;

use perf\Caching\CacheEntry;
use perf\Caching\Exception\CachingException;

class FileSystemCachingStorage implements CachingStorageInterface
{
    private const CACHE_FILE_SUFFIX        = '.cache';
    private const CACHE_METADATA_SEPARATOR = "\n";

    /**
     * Base path where cache files are stored.
     */
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim((string) $basePath, '/\\') . '/';
    }

    public function store(CacheEntry $cacheEntry): void
    {
        $cacheFilePath = $this->getCacheFilePath($cacheEntry->getId());

        $packedContent = serialize($cacheEntry->getContent());

        $metadata = [
            $cacheEntry->getCreationTimestamp(),
            ($cacheEntry->hasExpirationTimestamp() ? $cacheEntry->getExpirationTimestamp() : '-'),
            $packedContent,
        ];

        $fileContent = implode(self::CACHE_METADATA_SEPARATOR, $metadata);

        if (false === file_put_contents($cacheFilePath, $fileContent)) {
            throw new CachingException('Failed to store cache entry.');
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
            throw new CachingException('Failed to read cache file.');
        }

        // Extracting timestamps (creation and expiration) and packed content from file
        $exploded = explode(self::CACHE_METADATA_SEPARATOR, $fileContent, 3);
        if (3 !== count($exploded)) {
            throw new CachingException('Invalid cache file content.');
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
            throw new CachingException("Failed to delete cache file '{$cacheFilePath}'.");
        }
    }

    public function flushAll(): void
    {
        $mask = '*' . self::CACHE_FILE_SUFFIX;

        foreach (glob($this->basePath . $mask) as $cacheFilePath) {
            if (!unlink($cacheFilePath)) {
                throw new CachingException("Failed to delete cache file '{$cacheFilePath}'.");
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
