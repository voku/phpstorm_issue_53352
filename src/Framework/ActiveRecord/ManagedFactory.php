<?php

declare(strict_types=1);

/**
 * @template     T
 * @extends      Factory<T>
 *
 * @noinspection CompositionAndInheritanceInspection
 */
class ManagedFactory extends Factory
{

    /**
     * @var string
     */
    private $table;

    /**
     * @var string[]
     */
    private $fields = [];

    /**
     * @var string
     */
    protected $pk;

    public function __construct() {
        $this->autoInit();
    }

    /**
     * @return self
     */
    protected function autoInit(): self
    {
        /** @var ManagedFactory[] $HELPER_CACHE */
        static $HELPER_CACHE = [];

        if (isset($HELPER_CACHE[static::class])) {
            return $HELPER_CACHE[static::class];
        }

        $that = clone $this;

        $ClassName = \get_class($this);

        $that->setClass(\substr($ClassName, 0, -7));

        $ClassNameLower = \strtolower($that->classname);
        $that->pk = $ClassNameLower . '_id';
        $that->table = $ClassNameLower;

        $that->autoAddFields();

        $HELPER_CACHE[static::class] = $that;

        return $that;
    }

    /**
     * @return void
     */
    protected function autoAddFields(): void
    {
        foreach ((new ReflectionClass($this->classname))->getProperties() as $Property) {
            $PropertyName = $Property->getName();

            if (
                $PropertyName != 'factory'
                &&
                strncmp($PropertyName, '_', 1) !== 0
            ) {
                $this->addField($PropertyName);
            }
        }
    }

    /**
     * @param string      $columnName
     * @param null|string $phpName
     *
     * @return void
     */
    protected function addField(string $columnName, string $phpName = null): void
    {
        if ($phpName === null) {
            $phpName = $columnName;
        }

        $this->fields[$columnName] = $phpName;
    }

    /**
     * @param string $classname
     *
     * @return void
     *
     * @psalm-param class-string<T> $classname
     */
    protected function setClass(string $classname): void
    {
        if (\class_exists($classname) === false) {
            /** @noinspection ThrowRawExceptionInspection */
            throw new Exception('TODO');
        }

        if (\is_subclass_of($classname, ActiveRow::class) === false) {
            /** @noinspection ThrowRawExceptionInspection */
            throw new Exception('TODO');
        }

        $this->classname = $classname;
    }


    /**
     * @param int  $limit
     * @param int  $offset
     *
     * @return ActiveRow[]
     *
     * @psalm-return array<int,T>
     *
     * @noinspection NotOptimalIfConditionsInspection
     */
    public function fetchAll(int $limit = null, int $offset = null): array
    {
        $query = "select " . $this->getSelectFields() . " "
                 . "from `{$this->table}` "
                 . "order by " . $this->getIdColumn() . " ASC";

        if ($limit > 0 && $offset > 0) {
            $query .= " LIMIT " . (int)$offset . "," . (int)$limit . " ";
        } elseif ($limit > 0) {
            $query .= " LIMIT " . (int)$limit . " ";
        }

        return $this->fetchByQuery($query);
    }

    /**
     * @param string $query
     *
     * @return array<int,ActiveRow>
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     *
     * @psalm-return array<int,T>
     */
    public function fetchByQuery(string $query)
    {
        global $DB;

        assert($DB instanceof PDO);

        $result = $DB->query($query);

        $list = [];
        // @codingStandardsIgnoreStart
        /* @noinspection PhpAssignmentInConditionInspection */
        while ($row = $result->fetch()) {
            $list[] = new $this->classname($this, $row);
        }
        // @codingStandardsIgnoreEnd

        return $list;
    }

    /**
     * @return string
     */
    public function getIdColumn(): ?string
    {
        return $this->pk;
    }
    
    /**
     * @return string
     */
    public function getPrimaryKeyFieldName() {
        // DEBUG
        /*
        if (!isset($this->fields[$this->pk])) {
            print_r($this->pk);
            print_r($this->fields);
        }
         */

        $fieldname = $this->fields[$this->pk];

        return $fieldname;
    }

    /**
     * @param ActiveRow $activeRow
     *
     * @return int
     *
     * @phpstan-param T $activeRow
     */
    public function getIdValue($activeRow) {
        $fieldname = $this->getPrimaryKeyFieldName();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $activeRow->{$fieldname};
    }

    /**
     * @param null|string $alias
     * @param string      $prefix
     *
     * @return string
     */
    public function getSelectFields(string $alias = null, string $prefix = ''): string
    {
        if ($alias == null) {
            $alias = $this->table;
        }

        $sqlFields = [];
        foreach ($this->fields as $columnName => &$phpName) {
            $sqlFields[] = "`{$alias}`.`{$columnName}` as `{$prefix}{$phpName}` \n";
        }

        return \implode(', ', $sqlFields);
    }
}
