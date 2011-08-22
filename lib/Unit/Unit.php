<?php

namespace Unit;

use Unit\Reference\TableInterface;

class Unit extends Base
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
     * @var string
     */
    protected $symbol;

    /**
     * Get unit symbol.
     * 
     * @return string
     */
    public function getSymbol() {
        return $this->symbol;
    }

    /**
     * Does the symbol should be on the left.
     * 
     * @var bool
     */
    protected $symbolToLeft = FALSE;

    /**
     * Does the symbol should be on the left.
     * 
     * @return bool
     */
    public function symbolToLeft() {
        return $this->symbolToLeft;
    }

    /**
     * @var System
     */
    protected $system;

    /**
     * Get unit system.
     * 
     * @return System
     */
    public function getSystem() {
        return $this->system;
    }

    /**
     * Default round.
     * 
     * @var int
     */
    protected $defaultRound = 2;

    /**
     * Get default round.
     * 
     * @return int
     */
    public function getDefaultRound() {
        return $this->defaultRound;
    }

    /**
     * Tell if current unit is compatible with the given one.
     * 
     * @param Unit $unit
     * 
     * @return bool
     */
    public function isCompatibleWith(Unit $unit) {
        return $unit->type->getIdentifier() === $this->type->getIdentifier();
    }

    /**
     * Default constructor, never use it manually.
     * 
     * @param TableInterface $table
     * @param int|string $identifier
     * @param string $englishName
     * @param string $englishPlural
     * @param Type $type
     * @param string $symbol
     * @param System $system
     * @param bool $symbolToLeft = FALSE
     */
    public function __construct(TableInterface $table, $identifier, $englishName, $englishPlural, Type $type, $symbol, System $system, $symbolToLeft = FALSE) {
        parent::__construct($table, $identifier, $englishName, $englishPlural);
        $this->type         = $type;
        $this->symbol       = $symbol;
        $this->symbolToLeft = $symbolToLeft;
        $this->system       = $system;
    }
}
