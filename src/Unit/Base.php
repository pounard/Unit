<?php

namespace Unit;

use Unit\Reference\TableInterface;

abstract class Base
{
    /**
     * @var int|string
     */
    protected $identifier;

    /**
     * Get internal identifier.
     * 
     * @return int|string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * @var string
     */
    protected $englishName;

    /**
     * Get english name, suitable for translation.
     * 
     * @return int|string
     */
    public function getEnglishName() {
        return $this->englishName;
    }

    /**
     * @var string
     */
    protected $englishPlural;

    /**
     * Get english plural, suitable for translation.
     * 
     * @return int|string
     */
    public function getEnglishPlural() {
        return $this->englishPlural;
    }

    /**
     * @var TableInterface
     */
    protected $table;

    /**
     * Get reference table.
     * 
     * @return TableInterface
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Default constructor.
     * 
     * @param TableInterface $table
     * @param int|string $identifier
     * @param string $englishName
     * @param string $englishPlural
     */
    public function __construct(TableInterface $table, $identifier, $englishName, $englishPlural) {
        $this->table         = $table;
        $this->identifier    = $identifier;
        $this->englishName   = $englishName;
        $this->englishPlural = $englishPlural;
    }
}
