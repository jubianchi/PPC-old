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

    public function offset() : int
    {
        return $this->offset;
    }

    public function length() : int
    {
        return $this->length;
    }

    public function stream() : CharStream
    {
        return $this->stream;
    }

    public function equals(string $expected) : bool
    {
        $actual = (string) $this;

        return $actual === $expected;
    }

    public function matches(string $pattern) : bool
    {
        return preg_match($pattern, (string) $this) > 0;
    }

    public function merge(self $slice) : self
    {
        $first = $this;
        $last = $slice;

        if ($slice->offset() < $this->offset) {
            $first = $slice;
            $last = $this;
        }

        return new Slice($first->offset(), $last->offset() + $last->length() - $first->offset(), $first->stream());
    }

    public function __toString() : string
    {
        return $this->stream->cut($this->offset, $this->length);
    }
}
