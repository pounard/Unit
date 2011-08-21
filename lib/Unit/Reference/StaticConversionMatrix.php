<?php

namespace Unit\Reference;

use \Exception;

use Graph\Node;
use Unit\Type;
use Unit\Unit;

class StaticConversionMatrix implements ConversionMatrixInterface
{
  /**
   * @var bool
   */
  protected $_modified = FALSE;

  /**
   * Does this instance have been modified dynamically.
   * 
   * @return bool
   */
  public function isModified() {
    return $this->_modified;
  }

  /**
   * Full conversion matrixes, per type.
   * 
   * @var array
   */
  protected $_matrixes = array();

  /**
   * @var array
   */
  protected $_populated = array();

  /**
   * Populate the full matrix using data we have, for the given type.
   * 
   * @param Type $type
   */
  protected function _populateMatrix(Type $type) {
    $typeIdentifier = $type->getIdentifier();

    if (isset($this->_populated[$typeIdentifier])) {
      return;
    }

    $typeIdentifier = $type->getIdentifier();
    $matrix =& $this->_matrixes[$typeIdentifier];

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
        }
        else if (isset($matrix[$to][$from]) && !isset($matrix[$from][$to])) {
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
            }
            else {
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

    $this->_populated[$typeIdentifier] = TRUE;
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
    if (!isset($this->_matrixes[$typeIdentifier][$fromIdentifier][$toIdentifier])) {
      $this->_populateMatrix($type);
    }

    $factor = $this->_matrixes[$typeIdentifier][$fromIdentifier][$toIdentifier];

    if (!$factor instanceof Operation) {
      throw new Exception("No conversion factor could be found from " . $from->getEnglishName() . " to " . $to->getEnglishName());
    }

    return $factor;
  }

  /**
   * Add type specific conversion matrix.
   * 
   * @param Type $type
   * @param array $matrix
   */
  public function addMatrix(Type $type, array $matrix) {
    $this->_matrixes[$type->getIdentifier()] = $matrix;
  }

  /**
   * Get type specific conversion matrix.
   * 
   * @param Type $type
   * 
   * @return array
   */
  public function getMatrix(Type $type) {
    $typeIdentifier = $type->getIdentifier();

    if (!isset($this->_matrixes[$typeIdentifier])) {
      throw new Exception("Matrix for type " . $type->getEnglishName() . " does not exists");
    }

    return $this->_matrixes[$typeIdentifier];
  }

  /**
   * Get all conversion matrixes in one huge array.
   * 
   * @return array
   */
  public function getMatrixes() {
    return $this->_matrixes;
  }
}
