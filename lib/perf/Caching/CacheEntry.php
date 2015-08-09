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
    private $data;

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
     * @param mixed $data
     * @param int $creationTimestamp
     * @param null|int $expirationTimestamp
     * @return void
     */
    public function __construct($id, $data, $creationTimestamp, $expirationTimestamp)
    {
        $this->id                = (string) $id;
        $this->data              = $data;
        $this->creationTimestamp = (int) $creationTimestamp;

        if (is_null($expirationTimestamp)) {
            $this->expirationTimestamp = null;
        } else {
            $this->expirationTimestamp = (int) $expirationTimestamp;
        }
    }

    /**
     *
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     *
     *
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }

    /**
     *
     *
     * @return int
     */
    public function creationTimestamp()
    {
        return $this->creationTimestamp;
    }

    /**
     *
     *
     * @return null|int
     */
    public function expirationTimestamp()
    {
        return $this->expirationTimestamp;
    }
}
