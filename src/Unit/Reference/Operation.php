<?php

namespace Unit\Reference;

class Operation
{
    /**
     * @var float
     */
    protected $factor = 1;

    /**
     * Get factor.
     * 
     * @return int|float
     */
    public function getFactor() {
        return $this->factor;
    }

    /**
     * @var float
     */
    protected $delta = 0;

    /**
     * Get delta.
     * 
     * @return int|float
     */
    public function getDelta() {
      return $this->delta;
    }

    /**
     * Compute value following the operation internals.
     * 
     * @param int|float $a
     * @param int|float $b
     */
    public function compute($value) {
        return ($value * $this->factor) + $this->delta;
    }

    /**
     * Get opposite operation.
     * 
     * @return Operation
     */
    public function getOpposite() {
        return new self(1 / $this->factor, (0 - $this->delta));
    }

    /**
     * Combine with the given operation into a new instance.
     */
    public function getCombination(Operation $operation) {
        return new self($this->factor * $operation->factor, $this->delta + $operation->delta);
    }

    /**
     * Default constructor.
     * 
     * @param int|float $factor = 1
     * @param int|float $delta = 0;
     */
    public function __construct($factor = 1, $delta = 0) {
        $this->factor = $factor;
        $this->delta  = $delta;
    }
}
