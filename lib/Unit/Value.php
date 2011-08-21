<?php

namespace Unit;

use \Exception;

use Unit\Reference\ConversionMatrixInterface;
use Unit\Reference\TableInterface;

/**
 * Immutable value object.
 */
class Value
{
  /**
   * @var Unit
   */
  protected $_unit;

  /**
   * Get used unit.
   * 
   * @return Unit
   */
  public function getUnit() {
    return $this->_unit;
  }

  /**
   * @var int|float
   */
  protected $_value;

  /**
   * Return value.
   * 
   * @return int|float
   */
  public function getValue() {
    return $this->_value;
  }

  /**
   * Format current value with its unit formatting.
   * 
   * @param int|bool $round = TRUE
   *   Should the value be rounded.
   *     - If FALSE, raw value with maximum system precision will be displayed.
   *     - If TRUE, value will be rounded with unit default round.
   *     - If int, value will be rounded to this number of decimals.
   * @param string $symbolSeparator = ' '
   *   Symbol separator to use in display. 
   * 
   * @return string
   */
  public function format($round = TRUE, $symbolSeparator = ' ') {
    if (TRUE === $round) {
      $round = $this->_unit->getDefaultRound();
    }

    $value  = (FALSE === $round) ? $this->_value : round($this->_value, $round);
    $symbol = $this->_unit->getSymbol();

    if ($this->_unit->symbolToLeft()) {
      return $symbol . $symbolSeparator . $value;
    }
    else {
      return $value . $symbolSeparator . $symbol;
    }
  }

  public function __toString() {
    return $this->format();
  }

  /**
   * Convert the current instance to the given unit.
   * 
   * @param Unit $unit
   * 
   * @return Value
   */
  public function getConvertion(Unit $unit) {
    if (!$this->_unit->isCompatibleWith($unit)) {
      throw new Exception("Current unit " . $this->_unit->getEnglishName() . " is incompatible with " . $unit->getEnglishName());
    }

    $newValue = $this
      ->_unit
      ->getTable()
      ->getConversionMatrix()
      ->getOperation($this->_unit, $unit)
      ->compute($this->_value);

    return new Value($newValue, $unit);
  }

  /**
   * Convert in place to the current unit.
   * 
   * @param Unit $unit
   */
  public function convert(Unit $unit) {
    if (!$this->_unit->isCompatibleWith($unit)) {
      throw new Exception("Current unit " . $this->_unit->getEnglishName() . " is incompatible with " . $unit->getEnglishName());
    }

    $newValue = $this
      ->_unit
      ->getTable()
      ->getConversionMatrix()
      ->getOperation($this->_unit, $unit)
      ->compute($this->_value);

    $this->_unit  = $unit;
    $this->_value = $newValue;
  }

  /**
   * Construct new value.
   * 
   * @param int|float $value
   * @param Unit_Unit $unit = NULL
   */
  public function __construct($value, Unit $unit = NULL) {
    if (!is_float($value) && !is_int($value)) {
      throw new Exception("Invalid value " . $value);
    }

    // FIXME: To do later.
    if (NULL === $unit) {
      throw new Exception("Usage of NULL unit is not supported yet");
    }

    $this->_value = $value;
    $this->_unit = $unit;
  }
}
