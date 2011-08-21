<?php

namespace Unit;

use \Exception;

use Unit\Reference\TableInterface;

/**
 * Static helpers.
 */
class UnitHelper
{
  /**
   * @var TableInterface
   */
  protected static $_table;

  /**
   * Set conversion table.
   * 
   * @param TableInterface $table
   */
  public static function setTable(TableInterface $table) {
    self::$_table = $table;
  }

  /**
   * Convert value.
   * 
   * @param int|float $value
   * @param string|Unit $from
   * @param string|Unit $to
   */
  public static function convert($value, $from, $to) {
    if (!isset(self::$_table)) {
      throw new Exception("You must set the conversion table first.");
    }
    if (!$from instanceof Unit) {
      $from = self::$_table->getUnitBySymbol($from);
    }
    if (!$to instanceof Unit) {
      $to = self::$_table->getUnitBySymbol($to);
    }

    return self::$_table 
      ->getConversionMatrix()
      ->getOperation($from, $to)
      ->compute($value);
  }
}
