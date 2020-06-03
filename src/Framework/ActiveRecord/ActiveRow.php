<?php

declare(strict_types=1);

class ActiveRow
{

    /**
     * @var ManagedFactory<static>
     */
    public $factory;

    /**
     * @param Factory<ActiveRow>|ManagedFactory<static> $factory
     * @param null|array                                $row
     */
    public function __construct(Factory $factory, array $row = null) {
        $this->factory = &$factory;

        if ($row !== null) {
            $this->fromRow($row);
        }
    }

    /**
     * @param array $row
     *
     * @return void
     */
    public function fromRow(array $row): void {
        foreach ($row as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
