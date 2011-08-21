<?php

namespace Unit\Reference;

use \Exception;

use Unit\System;
use Unit\Type;
use Unit\Unit;

class StaticTable implements TableInterface {
  /**
   * @var ConversionMatrixInterface
   */
  protected $_conversionMatrix = array();

  /**
   * @var array
   *   Array of Type instances. Keyed by identifier.
   */
  protected $_types = array();

  /**
   * @var array
   *   Array of System instances. Keyed by identifier.
   */
  protected $_systems = array();

  /**
   * Denormalization for performances.
   * 
   * @var array
   *   Key/value pairs. Keys are type identifiers, values are numeric indexed
   *   arrays of System identifiers.
   */
  protected $_typeSystems = array();

  /**
   * @var array
   *   Array of Unit instances. Keyed by identifier.
   */
  protected $_units = array();

  /**
   * Denormalization for performances.
   * 
   * @var array
   *   Key/value pairs. Keys are type identifier, values are array of unit
   *   identifiers.
   */
  protected $_typeUnits = array();

  /**
   * Denormalization for performances.
   * 
   * @var array
   *   Key/value pairs. Keys are symbols, values are unit identifiers.
   */
  protected $_unitSymbols = array();

  public function getConversionMatrix() {
    return $this->_conversionMatrix;
  }

  public function getType($identifier) {
    if (!isset($this->_types[$identifier])) {
      throw new Exception("Unknow type identifier " . $identifier);
    }
    return $this->_types[$identifier];
  }

  public function getTypes() {
    return $this->_types;
  }

  public function getSystem($identifier) {
    if (!isset($this->_systems[$identifier])) {
      throw new Exception("Unknow system identifier " . $identifier);
    }
    return $this->_systems[$identifier];
  }

  public function getSystems(Type $type = NULL) {
    if (isset($type)) {
      $typeIdentifier = $type->getIdentifier();

      if (!isset($this->_types[$typeIdentifier])) {
        throw new Exception("Unknown type identifier " . $typeIdentifier);
      }

      $systems = array();

      foreach ($this->_typeSystems[$typeIdentifier] as $systemIdentifier) {
        $systems[$systemIdentifier] = $this->getSystem($systemIdentifier);
      }

      return $systems;
    }
    else {
      return $this->_systems;
    }
  }

  public function getUnit($identifier) {
    if (!isset($this->_units[$identifier])) {
      throw new Exception("Unknow unit identifier " . $identifier);
    }
    return $this->_units[$identifier];
  }

  public function getUnits(Type $type, System $system = NULL) {
    $typeIdentifier = $type->getIdentifier();

    if (!isset($this->_types[$typeIdentifier])) {
      throw new Exception("Unknown type identifier " . $typeIdentifier);
    }

    if (isset($system)) {
      // FIXME: Implement this.
      throw new Exception("Filtering units using system is not implemented yet");
    }
    else {
      $units = array();

      foreach ($this->_typeUnits[$typeIdentifier] as $unitIdentifier) {
        $units[$unitIdentifier] = $this->getUnit($unitIdentifier);
      }

      return $units;
    }
  }

  public function getUnitBySymbol($symbol) {
    if (!isset($this->_unitSymbols[$symbol])) {
      throw new Exception("Symbol " . $symbol . " is unknown");
    }
    return $this->getUnit($this->_unitSymbols[$symbol]);
  }
}
