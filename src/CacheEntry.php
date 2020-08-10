<?php

namespace perf\Caching;

use perf\Caching\Exception\CachingException;

class CacheEntry
{
    private string $id;

    /**
     * @var mixed
     */
    private $content;

    private int $creationTimestamp;

    private ?int $expirationTimestamp;

    /**
     * @param string   $id
     * @param mixed    $content
     * @param int      $creationTimestamp
     * @param null|int $expirationTimestamp
     */
    public function __construct(string $id, $content, int $creationTimestamp, ?int $expirationTimestamp)
    {
        $this->id                  = $id;
        $this->content             = $content;
        $this->creationTimestamp   = $creationTimestamp;
        $this->expirationTimestamp = $expirationTimestamp;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getCreationTimestamp(): int
    {
        return $this->creationTimestamp;
    }

    public function hasExpirationTimestamp(): bool
    {
        return (null !== $this->expirationTimestamp);
    }

    /**
     * @return int
     *
     * @throws CachingException
     */
    public function getExpirationTimestamp(): int
    {
        if ($this->hasExpirationTimestamp()) {
            return $this->expirationTimestamp;
        }

        throw new CachingException('No expiration timestamp set.');
    }
}
