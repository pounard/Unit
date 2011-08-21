<?php

namespace Unit;

use Unit\Reference\TableInterface;

/**
 * Represent a specific system.
 * 
 * Systems are singletons. These object are immutable and will be shared accross
 * all running code.
 */
class System extends Base {
  /**
   * @var Type
   */
  protected $_type;

  public function getType() {
    return $this->_type;
  }

  /**
   * Construct new instance.
   * 
   * @param TableInterface $table
   * @param int|string $identifier
   * @param string $englishName
   * @param Type $type
   */
  public function __construct(TableInterface $table, $identifier, $englishName, Type $type) {
    parent::__construct($table, $identifier, $englishName);

    $this->_type = $type;
  }
}
