<?php

namespace Graph;

use \Exception;

class Path
{
    /**
     * Global path weight.
     * 
     * @var float
     */
    public $weight;

    /**
     * Array of ordered Link instances.
     * 
     * @var array
     */
    public $links = array();

    /**
     * Compare to.
     * 
     * @param Path $path
     * 
     * @return float
     *   A negative float, zero, or a positive float as this object is less
     *   than, equal to, or greater than the specified object.
     */
    public function compareTo(Path $path) {
        return $this->weight - $path->weight;
    }

    /**
     * Append link.
     * 
     * @param Link $link
     */
    public function append(Link $link) {
        $this->links[] = $link;
        $this->weight += $link->weight;
    }

    /**
     * Prepend Link
     * 
     * @param Link $link
     */
    public function prepend(Link $link) {
        array_unshift($this->links, $link);
        $this->weight += $link->weight;
    }

    /**
     * Default constructor.
     * 
     * @param array $path
     * @param float $weight = NULL
     */
    public function __construct(array $links = NULL) {
        if (isset($links)) {
            foreach ($links as $link) {
                $this->append($link);
            }
        }
    }

    public function __toString() {
        $path = array();
        foreach ($this->links as $link) {
            $path[] = $link->from->name . '->' . $link->to->name;
        }
        return '[' . $this->weight . '] ' . implode(', ', $path);
    }
}
