<?php

namespace Unit;

use Unit\Reference\TableInterface;

/**
 * Shared implementation for Unit hardcoded system, type and unit descriptions.
 */
abstract class Base
{
  /**
   * @var int|string
   */
  protected $_identifier;

  /**
   * Get internal identifier.
   * 
   * @return int|string
   */
  public function getIdentifier() {
    return $this->_identifier;
  }

  /**
   * @var string
   */
  protected $_englishName;

  /**
   * Get english name, suitable for translation.
   * 
   * @return int|string
   */
  public function getEnglishName() {
    return $this->_englishName;
  }

  /**
   * @var TableInterface
   */
  protected $_table;

  /**
   * Get reference table.
   * 
   * @return TableInterface
   */
  public function getTable() {
    return $this->_table;
  }

  /**
   * Default constructor.
   * 
   * @param TableInterface $table
   * @param int|string $identifier
   * @param string $englishName
   */
  public function __construct(TableInterface $table, $identifier, $englishName) {
    $this->_table = $table;
    $this->_identifier = $identifier;
    $this->_englishName = $englishName;
  }
}
