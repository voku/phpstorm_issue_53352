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
    
    /**
     * @return mixed|static
     *                      <p>
     *                      We fake the return "static" here, because we want auto-completion for the current properties.
     *                      <br><br>
     *                      But here the properties contains only the name from the property itself:
     *                      <br>
     *                      <code>
     *                      $a = ActiveRow->name = "foo";
     *                      $m = $a->m();
     *                      echo $m->name; // "name"
     *                      </code>
     *                      </p>
     */
    public function m() {
        return (new ActiveRowMeta())->getMetaObject($this);
    }
    
    /**
     * Gibt den wert des ID-feldes zurück
     *
     * @return int
     */
    public function getId() {
        return $this->factory->getIdValue($this);
    }
}
