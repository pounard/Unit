<?php

namespace Unit;

use \Exception;

use Unit\Reference\ConversionMatrixInterface,
    Unit\Reference\TableInterface;

class Value
{
    /**
     * @var Unit
     */
    protected $unit;

    /**
     * Get used unit.
     * 
     * @return Unit
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * @var int|float
     */
    protected $value;

    /**
     * Return value.
     * 
     * @return int|float
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Format current value with its unit formating.
     * 
     * @param int|bool $round = TRUE
     *   Should the value be rounded.
     *     - If FALSE, raw value with maximum precision will be displayed.
     *     - If TRUE, value will be rounded with unit default round.
     *     - If int, value will be rounded to this number of decimals.
     * @param string $symbolSeparator = ' '
     *   Symbol separator to use in display. 
     * 
     * @return string
     */
    public function format($round = TRUE, $symbolSeparator = ' ') {
        if (TRUE === $round) {
            $round = $this->unit->getDefaultRound();
        }

        $value  = (FALSE === $round) ? $this->value : round($this->value, $round);
        $symbol = $this->unit->getSymbol();

        if ($this->unit->symbolToLeft()) {
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
        if (!$this->unit->isCompatibleWith($unit)) {
            throw new Exception("Current unit " . $this->unit->getEnglishName() . " is incompatible with " . $unit->getEnglishName());
        }

        $newValue = $this
            ->unit
            ->getTable()
            ->getConversionMatrix()
            ->getOperation($this->unit, $unit)
            ->compute($this->value);

        return new Value($newValue, $unit);
    }

    /**
     * Convert in place to the current unit.
     * 
     * @param Unit $unit
     */
    public function convert(Unit $unit) {
        if (!$this->unit->isCompatibleWith($unit)) {
            throw new Exception("Current unit " . $this->unit->getEnglishName() . " is incompatible with " . $unit->getEnglishName());
        }

        $newValue = $this
            ->unit
            ->getTable()
            ->getConversionMatrix()
            ->getOperation($this->unit, $unit)
            ->compute($this->value);

        $this->unit  = $unit;
        $this->value = $newValue;
    }

    /**
     * Construct new value.
     * 
     * @param int|float $value
     * @param Unit $unit = NULL
     */
    public function __construct($value, Unit $unit = NULL) {
        if (!is_float($value) && !is_int($value)) {
            throw new Exception("Invalid value " . $value);
        }

        // FIXME: To do later.
        if (NULL === $unit) {
            throw new Exception("Usage of NULL unit is not supported yet");
        }

        $this->value = $value;
        $this->unit = $unit;
    }
}
