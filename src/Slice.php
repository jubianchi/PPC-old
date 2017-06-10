<?php

declare(strict_types=1);

namespace PPC;

class Slice
{
    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $length;
    /**
     * @var CharStream
     */
    private $stream;

    public function __construct(int $offset, int $length, CharStream $stream)
    {
        $this->offset = $offset;
        $this->length = $length;
        $this->stream = $stream;
    }

    public function offset()
    {
        return $this->offset;
    }

    public function length()
    {
        return $this->length;
    }

    public function stream()
    {
        return $this->stream;
    }

    public function __toString()
    {
        return $this->stream->cut($this->offset, $this->length);
    }
}
