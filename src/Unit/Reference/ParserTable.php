<?php

namespace Unit\Reference;

use Unit\Parsing\ParserInterface,
    Unit\System,
    Unit\SystemDefault,
    Unit\Type,
    Unit\Unit;

use \Exception;

class ParserTable extends StaticTable
{
    public function __construct(ParserInterface $parser) {
        $parser->setTable($this);

        $this->conversionMatrix = new StaticConversionMatrix();

        // Register types.
        foreach ($parser->getTypes() as $type) {
            $typeIdentifier = $type->getIdentifier();
            $this->types[$typeIdentifier] = $type;

            // Register systems for this type.
            $systems = $parser->getSystems($type);
            if (empty($systems)) {
                $systems = array(new SystemDefault($this, $type));
            }

            foreach ($systems as $system) {
                $systemIdentifier = $system->getIdentifier();
                $this->systems[$systemIdentifier] = $system;
                $this->typeSystems[$typeIdentifier][] = $systemIdentifier;

                // Register Units for this system.
                foreach ($parser->getUnits($type, $system) as $unit) {
                    $unitIdentifier = $unit->getIdentifier();
                    $unitSymbol = $unit->getSymbol();
                    $this->units[$unitIdentifier] = $unit;
                    $this->typeUnits[$typeIdentifier][] = $unitIdentifier;
                    $this->unitSymbols[$unitSymbol] = $unitIdentifier;
                }
            }

            // Finally, register conversion matrix.
            $this->conversionMatrix->setMatrixData($type, $parser->getRawConversionMatrix($type));
        }
    }
}
