<?php

namespace Unit\Reference;

use Unit\System,
    Unit\Type,
    Unit\Unit;

use \Exception;

class StaticTable implements TableInterface
{
    /**
     * @var ConversionMatrixInterface
     */
    protected $conversionMatrix = array();

    /**
     * @var array
     *   Array of Type instances. Keyed by identifier.
     */
    protected $types = array();

    /**
     * @var array
     *   Array of System instances. Keyed by identifier.
     */
    protected $systems = array();

    /**
     * Denormalization for performances.
     * 
     * @var array
     *   Key/value pairs. Keys are type identifiers, values are numeric indexed
     *   arrays of System identifiers.
     */
    protected $typeSystems = array();

    /**
     * @var array
     *   Array of Unit instances. Keyed by identifier.
     */
    protected $units = array();

    /**
     * Denormalization for performances.
     * 
     * @var array
     *   Key/value pairs. Keys are type identifier, values are array of unit
     *   identifiers.
     */
    protected $typeUnits = array();

    /**
     * Denormalization for performances.
     * 
     * @var array
     *   Key/value pairs. Keys are symbols, values are unit identifiers.
     */
    protected $unitSymbols = array();

    public function getConversionMatrix() {
        return $this->conversionMatrix;
    }

    public function getType($identifier) {
        if (!isset($this->types[$identifier])) {
            throw new Exception("Unknow type identifier " . $identifier);
        }
        return $this->types[$identifier];
    }

    public function getTypes() {
        return $this->types;
    }

    public function getSystem($identifier) {
        if (!isset($this->systems[$identifier])) {
            throw new Exception("Unknow system identifier " . $identifier);
        }
        return $this->systems[$identifier];
    }

    public function getSystems(Type $type = NULL) {
        if (isset($type)) {
            $typeIdentifier = $type->getIdentifier();

            if (!isset($this->types[$typeIdentifier])) {
                throw new Exception("Unknown type identifier " . $typeIdentifier);
            }

            $systems = array();

            foreach ($this->typeSystems[$typeIdentifier] as $systemIdentifier) {
                $systems[$systemIdentifier] = $this->getSystem($systemIdentifier);
            }

            return $systems;
        } else {
            return $this->systems;
        }
    }

    public function getUnit($identifier) {
        if (!isset($this->units[$identifier])) {
            throw new Exception("Unknow unit identifier " . $identifier);
        }
        return $this->units[$identifier];
    }

    public function getUnits(Type $type, System $system = NULL) {
        $typeIdentifier = $type->getIdentifier();

        if (!isset($this->types[$typeIdentifier])) {
            throw new Exception("Unknown type identifier " . $typeIdentifier);
        }

        if (isset($system)) {
            // FIXME: Implement this.
            throw new Exception("Filtering units using system is not implemented yet");
        } else {
            $units = array();

            foreach ($this->typeUnits[$typeIdentifier] as $unitIdentifier) {
                $units[$unitIdentifier] = $this->getUnit($unitIdentifier);
            }

            return $units;
        }
    }

    public function getUnitBySymbol($symbol) {
        if (!isset($this->unitSymbols[$symbol])) {
            throw new Exception("Symbol " . $symbol . " is unknown");
        }
        return $this->getUnit($this->unitSymbols[$symbol]);
    }
}
