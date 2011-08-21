<?php

namespace Unit;

use Unit\Reference\TableInterface;

/**
 * Represent a specific unit.
 * 
 * Units are singletons, each one for each type/unit couple. These object are
 * immutable and will be shared accross all running code.
 */
class Unit extends Base
{
  /**
   * @var Type
   */
  protected $_type;

  /**
   * Get type.
   * 
   * @return Type
   */
  public function getType() {
    return $this->_type;
  }

  /**
   * @var string
   */
  protected $_symbol;

  /**
   * Get unit symbol.
   * 
   * @return string
   */
  public function getSymbol() {
    return $this->_symbol;
  }

  /**
   * Does the symbol should be on the left.
   * 
   * @var bool
   */
  protected $_symbolToLeft = FALSE;

  /**
   * Does the symbol should be on the left.
   * 
   * @return bool
   */
  public function symbolToLeft() {
    return $this->_symbolToLeft;
  }

  /**
   * @var System
   */
  protected $_system;

  /**
   * Get unit system.
   * 
   * @return System
   */
  public function getSystem() {
    return $this->_system;
  }

  /**
   * Default round.
   * 
   * @var int
   */
  protected $_defaultRound = 2;

  /**
   * Get default round.
   * 
   * @return int
   */
  public function getDefaultRound() {
    return $this->_defaultRound;
  }

  /**
   * Tell if current unit is compatible with the given one.
   * 
   * @param Unit $unit
   * 
   * @return bool
   */
  public function isCompatibleWith(Unit $unit) {
    return $unit->getType()->getIdentifier() === $this->_type->getIdentifier();
  }

  /**
   * Default constructor, never use it manually.
   * 
   * @param TableInterface $table
   * @param int|string $identifier
   * @param string $englishName
   * @param Type $type
   * @param string $symbol
   * @param System $system
   * @param bool $symbolToLeft = FALSE
   */
  public function __construct(TableInterface $table, $identifier, $englishName, Type $type, $symbol, System $system, $symbolToLeft = FALSE) {
    parent::__construct($table, $identifier, $englishName);
    $this->_type = $type;
    $this->_symbol = $symbol;
    $this->_symbolToLeft = $symbolToLeft;
    $this->_system = $system;
  }
}
