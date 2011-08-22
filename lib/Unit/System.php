<?php

namespace Unit;

use Unit\Reference\TableInterface;

class System extends Base
{
    /**
     * @var Type
     */
    protected $type;

    /**
     * Get type.
     * 
     * @return Type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Construct new instance.
     * 
     * @param TableInterface $table
     * @param int|string $identifier
     * @param string $englishName
     * @param string $englishName
     * @param Type $type
     */
    public function __construct(TableInterface $table, $identifier, $englishName, $englishPlural, Type $type) {
        parent::__construct($table, $identifier, $englishName, $englishPlural);
        $this->type = $type;
    }
}
