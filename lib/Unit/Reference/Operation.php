<?php

namespace Unit\Reference;

class Operation
{
  /**
   * @var float
   */
  protected $_factor = 1;

  /**
   * Get factor.
   * 
   * @return int|float
   */
  public function getFactor() {
    return $this->_factor;
  }

  /**
   * @var float
   */
  protected $_delta = 0;

  /**
   * Get delta.
   * 
   * @return int|float
   */
  public function getDelta() {
    return $this->_delta;
  }

  /**
   * Compute value following the operation internals.
   * 
   * @param int|float $a
   * @param int|float $b
   */
  public function compute($value) {
    return $value * $this->_factor + $this->_delta;
  }

  /**
   * Get opposite operation.
   * 
   * @return Operation
   */
  public function getOpposite() {
    return new self(1 / $this->_factor, 0 - $this->_delta);
  }

  /**
   * Combine with the given operation into a new instance.
   */
  public function getCombination(Operation $operation) {
    return new self($this->_factor * $operation->_factor, $this->_delta + $operation->_delta);
  }

  /**
   * Default constructor.
   * 
   * @param int|float $factor = 1
   * @param int|float $delta = 0;
   */
  public function __construct($factor = 1, $delta = 0) {
    $this->_factor = $factor;
    $this->_delta  = $delta;
  }
}
