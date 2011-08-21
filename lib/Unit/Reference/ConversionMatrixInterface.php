<?php

namespace Unit\Reference;

use \Exception;

use Unit\Type;
use Unit\Unit;

interface ConversionMatrixInterface
{
  /**
   * Get operation for converting from the first unit to the second unit.
   * 
   * @param Unit $from
   * @param Unit $to
   * 
   * @return Operation
   * 
   * @throws Exception
   */
  public function getOperation(Unit $from, Unit $to);
}
