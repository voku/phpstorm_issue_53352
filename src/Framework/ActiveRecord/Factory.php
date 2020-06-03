<?php

declare(strict_types=1);

/**
 * @template T
 */
abstract class Factory
{

    /**
     * @var string
     *
     * @internal
     *
     * @psalm-var class-string<T>
     */
    protected $classname;

    /**
     * @return static
     */
    public static function create() {
        return new static();
    }
}
