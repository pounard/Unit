<?php

namespace Unit;

use Unit\Reference\TableInterface;

class SystemDefault extends System
{
    /**
     * @var int
     */
    protected static $identifierDecrement = 0;

    /**
     * Construct new instance.
     * 
     * @param TableInterface $table
     * @param Type $type
     */
    public function __construct(TableInterface $table, Type $type) {
        parent::__construct($table, --self::$identifierDecrement, "Default", NULL, $type);
    }
}
