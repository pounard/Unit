<?php

namespace Unit\Reference;

use Unit\Parsing\ParserInterface;

use \Exception;
use \SimpleXmlElement;

use Unit\System;
use Unit\SystemDefault;
use Unit\Type;
use Unit\Unit;

class ParserTable extends StaticTable
{
  public function __construct(ParserInterface $parser) {
    $parser->setTable($this);

    $this->_conversionMatrix = new StaticConversionMatrix();

    // Register types.
    foreach ($parser->getTypes() as $type) {
      $typeIdentifier = $type->getIdentifier();
      $this->_types[$typeIdentifier] = $type;

      // Register systems for this type.
      $systems = $parser->getSystems($type);
      if (empty($systems)) {
        $systems = array(new SystemDefault($this, $type));
      }
      foreach ($systems as $system) {
        $systemIdentifier = $system->getIdentifier();
        $this->_systems[$systemIdentifier] = $system;
        $this->_typeSystems[$typeIdentifier][] = $systemIdentifier;

        // Register Units for this system.
        foreach ($parser->getUnits($type, $system) as $unit) {
          $unitIdentifier = $unit->getIdentifier();
          $unitSymbol = $unit->getSymbol();
          $this->_units[$unitIdentifier] = $unit;
          $this->_typeUnits[$typeIdentifier][] = $unitIdentifier;
          $this->_unitSymbols[$unitSymbol] = $unitIdentifier;
        }
      }

      // Finally, register conversion matrix.
      $this->_conversionMatrix->addMatrix($type, $parser->getRawConversionMatrix($type));
    }
  }
}
