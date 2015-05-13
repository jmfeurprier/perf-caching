<?php

namespace perf\Caching;

/**
 *
 *
 */
class CacheEntry
{

    /**
     *
     *
     * @var string
     */
    private $id;

    /**
     *
     *
     * @var string
     */
    private $content;

    /**
     *
     *
     * @var int
     */
    private $creationTimestamp;

    /**
     * Optional expiration timestamp.
     *
     * @var null|int
     */
    private $expirationTimestamp;

    /**
     * Constructor.
     *
     * @param string $id
     * @param mixed $content
     * @param int $creationTimestamp
     * @param null|int $expirationTimestamp
     * @return void
     */
    public function __construct($id, $content, $creationTimestamp, $expirationTimestamp)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException("Invalid cache entry Id.");
        }

        if (!is_int($creationTimestamp)) {
            throw new \InvalidArgumentException("Invalid cache entry creation timestamp.");
        }

        if ((null !== $expirationTimestamp) && !is_int($expirationTimestamp)) {
            throw new \InvalidArgumentException("Invalid cache entry expiration timestamp.");
        }

        $this->id                  = $id;
        $this->content             = $content;
        $this->creationTimestamp   = $creationTimestamp;
        $this->expirationTimestamp = $expirationTimestamp;
    }

    /**
     *
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     *
     *
     * @return int
     */
    public function getCreationTimestamp()
    {
        return $this->creationTimestamp;
    }

    /**
     *
     *
     * @return bool
     */
    public function hasExpirationTimestamp()
    {
        return (null !== $this->expirationTimestamp);
    }

    /**
     *
     *
     * @return int
     */
    public function getExpirationTimestamp()
    {
        if ($this->hasExpirationTimestamp()) {
            return $this->expirationTimestamp;
        }

        throw new \RuntimeException('No expiration timestamp set.');
    }
}
