<?php

namespace Unit\Parsing;

use Unit\Reference\Operation,
    Unit\Reference\TableInterface,
    Unit\System,
    Unit\SystemDefault,
    Unit\Type,
    Unit\Unit;

use \Exception,
    \SimpleXmlElement;

class ParserSimpleXml implements ParserInterface
{
    /**
     * @var Table
     */
    protected $table;

    public function setTable(TableInterface $table) {
        $this->table = $table;
    }

    /**
     * @var SimpleXmlElement
     */
    protected $xml;

    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var array
     */
    protected $systems = array();

    /**
     * @var array
     */
    protected $units = array();

    /**
     * @var array
     */
    protected $conversionRawMatrix = array();

    protected function parseType(SimpleXmlElement $node) {
        $attributes = $node->attributes();

        $identifier    = (string)$attributes->code;
        $englishName   = (string)$attributes->name;
        $englishPlural = (string)$attributes->plural;

        if (empty($identifier) || empty($englishName)) {
            throw new Exception("Incomplete type found.");
        }

        return new Type($this->table, $identifier, $englishName, $englishPlural);
    }

    public function getTypes() {
        $nodes = $this->xml->xpath('/UnitTable/Types/Type');

        if (empty($nodes)) {
            throw new Exception("Invalid file, no types are registered.");
        }

        foreach ($nodes as $node) {
            $type = $this->parseType($node);
            $this->types[$type->getIdentifier()] = $type;
        }

        return $this->types;
    }

    protected function parseSystem(SimpleXMLElement $node, Type $type) {
        $attributes = $node->attributes();

        $identifier    = (string)$attributes->code;
        $englishName   = (string)$attributes->name;
        $englishPlural = (string)$attributes->plural;

        if (empty($identifier) || empty($englishName)) {
            throw new Exception("Incomplete system found.");
        }

        return new System($this->table, $identifier, $englishName, $englishPlural, $type);
    }

    public function getSystems(Type $type) {
        $typeIdentifier = $type->getIdentifier();

        if (isset($this->systems[$typeIdentifier])) {
            return $this->systems[$typeIdentifier];
        }

        $this->systems[$typeIdentifier] = array();

        $nodes = $this->xml->xpath('/UnitTable/Systems/System[@type="' . $typeIdentifier . '"]');

        if (empty($nodes)) {
            return $this->systems[$typeIdentifier];
        }

        foreach ($nodes as $node) {
            $system = $this->parseSystem($node, $type);
            $this->systems[$typeIdentifier][$system->getIdentifier()] = $system;
        }

        return $this->systems[$typeIdentifier];
    }

    protected function parseConvert(Unit $unit, SimpleXMLElement $node, Type $type) {
        $typeIdentifier = $type->getIdentifier();
        $unitIdentifier = $unit->getIdentifier();

        foreach ($node->xpath('Convert') as $convertNode) {
            $attributes = $convertNode->attributes();

            $to     = (string)$attributes->to;
            $factor = (string)$attributes->factor;
            $delta  = (string)$attributes->delta;

            // FIXME: Something needs fixing here: converting to int always
            // gives a false positive, if attribute is not set it will silentely
            // give us an empty string, which will give 0.
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

            $this->conversionRawMatrix[$typeIdentifier][$unitIdentifier][$to] = new Operation($factor, $delta);
        }
    }

    protected function parseUnit(SimpleXMLElement $node, Type $type, System $system) {
        $attributes = $node->attributes();

        $identifier   = (string)$attributes->code;
        $englishName   = (string)$attributes->name;
        $englishPlural = (string)$attributes->plural;
        $symbol        = (string)$attributes->symbol;
        $symbolToLeft  = ("true" === ((string)$attributes->symbolToLeft));

        if (empty($identifier) || empty($englishName) || empty($symbol)) {
            throw new Exception("Incomplete unit found.");
        }

        $unit = new Unit($this->table, $identifier, $englishName, $englishPlural, $type, $symbol, $system, $symbolToLeft);

        $this->parseConvert($unit, $node, $type);

        return $unit;
    }

    public function getUnits(Type $type, System $system) {
        $typeIdentifier = $type->getIdentifier();
        $systemIdentifier = $system->getIdentifier();

        if (isset($this->units[$typeIdentifier][$systemIdentifier])) {
            return $this->units[$typeIdentifier][$systemIdentifier];
        }

        $this->units[$typeIdentifier][$systemIdentifier] = array();

        if ($system instanceof SystemDefault) {
            $xPath = '/UnitTable/Units/Unit[@type="' . $typeIdentifier . '"]';
        } else {
            $xPath = '/UnitTable/Units/Unit[@type="' . $typeIdentifier . '"][@system="' . $systemIdentifier . '"]';
        }
        $nodes = $this->xml->xpath($xPath);

        if (empty($nodes)) {
            return $this->units[$typeIdentifier][$systemIdentifier];
        }

        foreach ($nodes as $node) {
            $unit = $this->parseUnit($node, $type, $system);
            $this->units[$typeIdentifier][$systemIdentifier][$unit->getIdentifier()] = $unit;
        }

        return $this->units[$typeIdentifier][$systemIdentifier];
    }

    public function getRawConversionMatrix(Type $type) {
        $typeIdentifier = $type->getIdentifier();
        return isset($this->conversionRawMatrix[$typeIdentifier]) ? $this->conversionRawMatrix[$typeIdentifier] : array();
    }

    public function __construct($filePath) {
        $this->xml = new SimpleXMLElement($filePath, NULL, TRUE);
    }
}
