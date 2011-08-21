<?php

namespace Unit\Parsing;

use Unit\Reference\Operation;

use Unit\SystemDefault;

use \Exception;
use \SimpleXmlElement;

use Unit\Reference\TableInterface;
use Unit\System;
use Unit\Type;
use Unit\Unit;

class ParserSimpleXml implements ParserInterface
{
  /**
   * @var Table
   */
  protected $_table;

  public function setTable(TableInterface $table) {
    $this->_table = $table;
  }

  /**
   * @var SimpleXmlElement
   */
  protected $_xml;

  /**
   * @var array
   */
  protected $_types = array();

  /**
   * @var array
   */
  protected $_systems = array();

  /**
   * @var array
   */
  protected $_units = array();

  /**
   * @var array
   */
  protected $_conversionRawMatrix = array();

  protected function _parseType(SimpleXmlElement $node) {
    $attributes = $node->attributes();

    $identifier = (string)$attributes->code;
    $englishName = (string)$attributes->name;

    if (empty($identifier) || empty($englishName)) {
      throw new Exception("Incomplete type found.");
    }

    return new Type($this->_table, $identifier, $englishName);
  }

  public function getTypes() {
    $nodes = $this->_xml->xpath('/UnitTable/Types/Type');

    if (empty($nodes)) {
      throw new Exception("Invalid file, no types are registered.");
    }

    foreach ($nodes as $node) {
      $type = $this->_parseType($node);
      $this->_types[$type->getIdentifier()] = $type;
    }

    return $this->_types;
  }

  protected function _parseSystem(SimpleXMLElement $node, Type $type) {
    $attributes = $node->attributes();

    $identifier = (string)$attributes->code;
    $englishName = (string)$attributes->name;

    if (empty($identifier) || empty($englishName)) {
      throw new Exception("Incomplete system found.");
    }

    return new System($this->_table, $identifier, $englishName, $type);
  }

  public function getSystems(Type $type) {
    $typeIdentifier = $type->getIdentifier();

    if (isset($this->_systems[$typeIdentifier])) {
      return $this->_systems[$typeIdentifier];
    }

    $this->_systems[$typeIdentifier] = array();

    $nodes = $this->_xml->xpath('/UnitTable/Systems/System[@type="' . $typeIdentifier . '"]');

    if (empty($nodes)) {
      return $this->_systems[$typeIdentifier];
    }

    foreach ($nodes as $node) {
      $system = $this->_parseSystem($node, $type);
      $this->_systems[$typeIdentifier][$system->getIdentifier()] = $system;
    }

    return $this->_systems[$typeIdentifier];
  }

  protected function _parseConvert(Unit $unit, SimpleXMLElement $node, Type $type) {
    $typeIdentifier = $type->getIdentifier();
    $unitIdentifier = $unit->getIdentifier();

    foreach ($node->xpath('Convert') as $convertNode) {
      $attributes = $convertNode->attributes();

      $to     = (string)$attributes->to;
      $factor = (string)$attributes->factor;
      $delta  = (string)$attributes->delta;

      // FIXME: Something needs fixing here: converting to int always gives a
      // false positive, if attribute is not set it will silentely give us
      // an empty string, which will give 0.
      if (!strlen($factor)) {
        $factor = 1;
      }
      else if (is_int($factor)) {
        $factor = (int)$factor;
      }
      else {
        $factor = (float)$factor;
      }

      if (!strlen($delta)) {
        $delta = 0;
      }
      else if (is_int($delta)) {
        $delta = (int)$delta;
      }
      else {
        $delta = (float)$delta;
      }

      if (empty($to) || (empty($factor) && empty($delta))) {
        throw new Exception("Incomplete convert data found.");
      }

      $this->_conversionRawMatrix[$typeIdentifier][$unitIdentifier][$to] = new Operation($factor, $delta);
    }
  }

  protected function _parseUnit(SimpleXMLElement $node, Type $type, System $system) {
    $attributes = $node->attributes();

    $identifier = (string)$attributes->code;
    $englishName = (string)$attributes->name;
    $symbol = (string)$attributes->symbol;
    $symbolToLeft = ("true" === ((string)$attributes->symbolToLeft));

    if (empty($identifier) || empty($englishName) || empty($symbol)) {
      throw new Exception("Incomplete unit found.");
    }

    $unit = new Unit($this->_table, $identifier, $englishName, $type, $symbol, $system, $symbolToLeft);

    $this->_parseConvert($unit, $node, $type);

    return $unit;
  }

  public function getUnits(Type $type, System $system) {
    $typeIdentifier = $type->getIdentifier();
    $systemIdentifier = $system->getIdentifier();

    if (isset($this->_units[$typeIdentifier][$systemIdentifier])) {
      return $this->_units[$typeIdentifier][$systemIdentifier];
    }

    $this->_units[$typeIdentifier][$systemIdentifier] = array();

    if ($system instanceof SystemDefault) {
      $xPath = '/UnitTable/Units/Unit[@type="' . $typeIdentifier . '"]';
    }
    else {
      $xPath = '/UnitTable/Units/Unit[@type="' . $typeIdentifier . '"][@system="' . $systemIdentifier . '"]';
    }
    $nodes = $this->_xml->xpath($xPath);

    if (empty($nodes)) {
      return $this->_units[$typeIdentifier][$systemIdentifier];
    }

    foreach ($nodes as $node) {
      $unit = $this->_parseUnit($node, $type, $system);
      $this->_units[$typeIdentifier][$systemIdentifier][$unit->getIdentifier()] = $unit;
    }

    return $this->_units[$typeIdentifier][$systemIdentifier];
  }

  public function getRawConversionMatrix(Type $type) {
    $typeIdentifier = $type->getIdentifier();
    return isset($this->_conversionRawMatrix[$typeIdentifier]) ? $this->_conversionRawMatrix[$typeIdentifier] : array();
  }

  public function __construct($filePath) {
    $this->_xml = new SimpleXMLElement($filePath, NULL, TRUE);
  }
}
