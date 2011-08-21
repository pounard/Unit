<?php

namespace Unit\Reference;

use \Exception;

use Unit\System;
use Unit\Type;
use Unit\Unit;

interface TableInterface {
  /**
   * Get conversion matrix.
   * 
   * @return ConversionMatrixInterface
   */
  public function getConversionMatrix();

  /**
   * Get specific type by identifier.
   * 
   * @param int|string $identifier
   * 
   * @return Type
   * 
   * @throws Exception
   */
  public function getType($identifier);

  /**
   * Get all know types.
   * 
   * @return array
   *   Array of Type instances.
   */
  public function getTypes();

  /**
   * Get all known systems.
   * 
   * @param int|string $identifier
   * 
   * @return System
   * 
   * @throws Exception
   */
  public function getSystem($identifier);

  /**
   * Get all known systems.
   * 
   * @param Type $type = NULL
   *   Filter by type.
   * 
   * @return array
   *   Array of System instances.
   * 
   * @throws Exception
   */
  public function getSystems(Type $type = NULL);

  /**
   * Get specific unit by identifier.
   * 
   * @param int|string $identifier
   * 
   * @return Unit
   * 
   * @throws Exception
   */
  public function getUnit($identifier);

  /**
   * Get known units for the given type.
   * 
   * @param Type $type
   * @param System $system = NULL
   *   Filter by system.
   * 
   * @return array
   *   Array of Unit instances.
   * 
   * @throws Exception
   */
  public function getUnits(Type $type, System $system = NULL);

  /**
   * Get a specific unit using the international suffix given.
   * 
   * @param string $symbol
   * 
   * @return Unit
   * 
   * @throws Exception
   */
  public function getUnitBySymbol($symbol);
}
