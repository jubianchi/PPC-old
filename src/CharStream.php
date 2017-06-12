<?php

declare(strict_types=1);

namespace PPC;

use OutOfBoundsException;
use SeekableIterator;

class CharStream implements SeekableIterator
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var int
     */
    private $position;

    public function __construct(string $string)
    {
        $this->string = $string;

        $this->rewind();
    }

    public function current()
    {
        return mb_substr($this->string, $this->position, 1);
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return $this->position < mb_strlen($this->string);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function seek($position)
    {
        if ($position > mb_strlen($this->string)) {
            throw new OutOfBoundsException();
        }

        $this->position = $position;
    }

    public function cut(int $offset, int $length)
    {
        return mb_substr($this->string, $offset, $length);
    }
}
