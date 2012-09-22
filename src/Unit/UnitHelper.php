<?php

namespace Unit;

use Unit\Reference\TableInterface;

use \Exception;

class UnitHelper
{
    /**
     * @var TableInterface
     */
    protected static $table;

    /**
     * Set conversion table.
     * 
     * @param TableInterface $table
     */
    public static function setTable(TableInterface $table) {
        self::$table = $table;
    }

    /**
     * Convert value.
     * 
     * @param int|float $value
     * @param string|Unit $from
     * @param string|Unit $to
     */
    public static function convert($value, $from, $to) {
        if (!isset(self::$table)) {
            throw new Exception("You must set the conversion table first.");
        }
        if (!$from instanceof Unit) {
            $from = self::$table->getUnitBySymbol($from);
        }
        if (!$to instanceof Unit) {
            $to = self::$table->getUnitBySymbol($to);
        }

        return self::$table 
            ->getConversionMatrix()
            ->getOperation($from, $to)
            ->compute($value);
    }
}
