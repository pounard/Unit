<?php

namespace Unit\Parsing;

use Unit\Reference\TableInterface;
use Unit\System;
use Unit\Type;
use Unit\Unit;

interface ParserInterface
{
  /**
   * Set table before parsing.
   * 
   * @param Table $table
   */
  public function setTable(TableInterface $table);

  /**
   * Parse types.
   * 
   * @return array
   *   Array of registered types.
   */
  public function getTypes();

  /**
   * Parse systems for the given type.
   * 
   * If the returned array is empty the default implementation will be used.
   * 
   * @param Type $type
   * 
   * @return array
   *   Array of registered systems for this type.
   */
  public function getSystems(Type $type);

  /**
   * Parse units for the type.
   * 
   * If system is set an instance of SystemDefault, parse all units for the
   * given type instead, and set the given system as units system.
   * 
   * @param Type $type
   * @param System $system
   * 
   * @return array
   *   Array of registered units.
   */
  public function getUnits(Type $type, System $system);

  /**
   * Parse the full conversion matrix for the given type.
   * 
   * @return array
   *   Factor matrix, indexed horizontally and vertically using the source
   *   then destination unit identifier. Values inside the matrix are
   *   Operation instances.
   *   This is not a real matrix and it might carry blanks values. The
   *   runtime ConversionMatrix object will attempt to fill the blanks
   *   itself if they exists. 
   */
  public function getRawConversionMatrix(Type $type);
}
