<?php

namespace Unit\Reference;

use Graph\Node,
    Unit\Type,
    Unit\Unit;

use \Exception;

class StaticConversionMatrix implements ConversionMatrixInterface
{
    /**
     * @var bool
     */
    protected $modified = FALSE;

    /**
     * Does this instance have been modified dynamically.
     * 
     * @return bool
     */
    public function isModified() {
        return $this->modified;
    }

    /**
     * Full conversion matrixes, per type.
     * 
     * @var array
     */
    protected $matrixes = array();

    /**
     * @var array
     */
    protected $populated = array();

    /**
     * Populate the full matrix using data we have, for the given type.
     * 
     * @param Type $type
     */
    protected function populateMatrix(Type $type) {
        $typeIdentifier = $type->getIdentifier();

        if (!isset($this->matrixes[$typeIdentifier]) || isset($this->populated[$typeIdentifier])) {
            return;
        }

        $typeIdentifier = $type->getIdentifier();
        $matrix =& $this->matrixes[$typeIdentifier];

        $keys = $nodes = array();

        // Find all nodes, even if non related.
        foreach ($matrix as $from => $toArray) {
            $keys[$from] = $from;
            $nodes[$from] = $fromNode = new Node($from);
            foreach ($toArray as $to => $factor) {
                $keys[$to] = $to;
                if (!isset($nodes[$to])) { // Could have dups.
                    $nodes[$to] = $toNode = new Node($to);
                }
            }
        }

        // Populate the search graph.
        foreach ($keys as $from) {
            foreach ($keys as $to) {
                $link = FALSE;

                // Start by populating the full inverse matrix, in two pass, for
                // later graph optimization.
                if (isset($matrix[$from][$to]) && !isset($matrix[$to][$from])) {
                    $matrix[$to][$from] = $matrix[$from][$to]->getOpposite();
                    $link = TRUE;
                } else if (isset($matrix[$to][$from]) && !isset($matrix[$from][$to])) {
                    $matrix[$from][$to] = $matrix[$to][$from]->getOpposite();
                    $link = TRUE;
                }

                // Also populate node and links.
                if ($link) {
                    $nodes[$to]->addLink($nodes[$from]);
                    $nodes[$from]->addLink($nodes[$to]);
                }
            }
        }

        // All nodes have been populated, now iterate over all without exceptions,
        // skip those that already exists in matrix, and compute a path for those
        // who doesn't.
        // Each time we reach a new path, we also compute the new factor applying
        // successive multiplications corresponding to the path.
        foreach ($nodes as $from => $fromNode) {
            foreach ($nodes as $to => $toNode) {
                if ($from != $to && !isset($matrix[$from][$to])) {
                    // Find the path.
                    $path = $fromNode->find($toNode);

                    if (FALSE === $path) {
                        // Impossible path.
                        $matrix[$from][$to] = $matrix[$to][$from] = FALSE;
                    }

                    $operation = NULL;

                    foreach ($path->links as $link) {
                        if (!isset($operation)) {
                            $operation = $matrix[$link->from->name][$link->to->name];
                        } else {
                            $operation = $operation->getCombination($matrix[$link->from->name][$link->to->name]);
                        }
                    }

                    // Save new factor and its counter part: this will accellerate the
                    // algorithm.
                    $matrix[$from][$to] = $operation;
                    $matrix[$to][$from] = $operation->getOpposite();
                }
            }
        }

        $this->populated[$typeIdentifier] = TRUE;
    }

    public function getOperation(Unit $from, Unit $to) {
        if (!$from->isCompatibleWith($to)) {
            throw new Exception("Cannot convert from " . $from->getEnglishName() . ' to ' . $to->getEnglishName());
        }

        $type           = $from->getType();
        $typeIdentifier = $type->getIdentifier();
        $fromIdentifier = $from->getIdentifier();
        $toIdentifier   = $to->getIdentifier();

        // First check for direct conversion factor.
        if (!isset($this->matrixes[$typeIdentifier][$fromIdentifier][$toIdentifier])) {
            $this->populateMatrix($type);
        }

        $factor = $this->matrixes[$typeIdentifier][$fromIdentifier][$toIdentifier];

        if (!$factor instanceof Operation) {
            throw new Exception("No conversion factor could be found from " . $from->getEnglishName() . " to " . $to->getEnglishName());
        }

        return $factor;
    }

    /**
     * Set matrix data associated to the given type.
     * 
     * @param Type $type
     * @param array $data
     */
    public function setMatrixData(Type $type, array $data) {
        $this->matrixes[$type->getIdentifier()] = $data;
    }
}
