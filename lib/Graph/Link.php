<?php

namespace Graph;

class Link
{
  /**
   * Default weight if none specified.
   */
  const WEIGHT_DEFAULT = 1;

  /**
   * @var float
   */
  public $weight;

  /**
   * @var Node
   */
  public $from;

  /**
   * @var Node
   */
  public $to;

  /**
   * Compare to.
   * 
   * @param Path $path
   * 
   * @return float
   *   A negative float, zero, or a positive float as this object is less than,
   *   equal to, or greater than the specified object.
   */
  public function compareTo(Link $link) {
    return $this->weight - $link->weight;
  }

  /**
   * Default constructor.
   * 
   * @param Node $node
   * @param float $weight = Link::WEIGHT_DEFAULT
   */
  public function __construct(Node $from, Node $to, $weight = Link::WEIGHT_DEFAULT) {
    $this->from   = $from;
    $this->to     = $to;
    $this->weight = $weight;
  }
}
