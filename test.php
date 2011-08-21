<?php

// use Graph\Node;
use Unit\Parsing\ParserSimpleXml;
use Unit\Reference\ParserTable;
use Unit\Unit;
use Unit\UnitHelper;
use Unit\Value;

define('UNIT_BASE_DIR', __DIR__ . '/lib');

spl_autoload_register(function ($classname) {
  $parts = explode('\\', $classname);

  if ('Unit' === $parts[0] || 'Graph' === $parts[0]) {
    $filename = UNIT_BASE_DIR . '/' . implode('/', $parts) . '.php';

    if (file_exists($filename)) {
      require_once $filename;
    }
  }
  
  return FALSE;
});

/*
 * Graph test.
 */

/*
$a = new Node('a');
$b = new Node('b');
$c = new Node('c');
$d = new Node('d');
$e = new Node('e');
$f = new Node('f');
$g = new Node('g');

$a->addLink($b);
$a->addLink($c);
$a->addLink($d);
$a->addLink($e);

$b->addLink($d);
$b->addLink($e);

$d->addLink($e);

$e->addLink($f);
$e->addLink($g);

print "a to f: " . $a->find('f') . "\n";
print "b to g: " . $b->find('g') . "\n";
print "e to f: " . $e->find($f) . "\n";

 */

/*
 * Parser test.
 */

$timeBegin   = microtime(TRUE);
$memoryBegin = memory_get_usage();

$table       = new ParserTable(new ParserSimpleXml(__DIR__ . '/default/various.xml'));

$timeParse   = microtime(TRUE);
$memoryParse = memory_get_usage();

$unitMeter   = $table->getUnitBySymbol('m');
$unitFeet    = $table->getUnitBySymbol('ft');
$valueMeter  = new Value(12, $unitMeter);

print "Original value: " . $valueMeter . "\n";

$valueFeet   = $valueMeter->getConvertion($unitFeet);

$timeEnd     = microtime(TRUE);
$memoryEnd   = memory_get_usage();

print "Converted value " . $valueFeet . "\n";

UnitHelper::setTable($table);

print "Some conversions:\n";
print "12 m in ft: "    . UnitHelper::convert(12,   'm',  'ft') . "\n";
print "27 in in cm: "   . UnitHelper::convert(27,   'in', 'cm') . "\n";
print "5.12 km in m: "  . UnitHelper::convert(5.12, 'km', 'm')  . "\n";
print "20 °C in °K: "   . UnitHelper::convert(20,   'C',  'K')  . "\n";
print "20 °C in °F: "   . UnitHelper::convert(20,   'C',  'F')  . "\n";

$memoryFree   = memory_get_usage();

$b  = $table->getUnitBySymbol('b');
$kb = $table->getUnitBySymbol('kb');
$ms = $table->getUnitBySymbol('ms');

$memBefore   = new Value($memoryBegin,                $b);
$memAfter    = new Value($memoryFree,                 $b);
$memGlobal   = new Value($memoryEnd   - $memoryBegin, $b);
$memCompute  = new Value($memoryEnd   - $memoryParse, $b);
$memParse    = new Value($memoryParse - $memoryBegin, $b);
$timeGlobal  = new Value($timeEnd     - $timeBegin,   $ms);
$timeCompute = new Value($timeEnd     - $timeParse,   $ms);
$timeParse   = new Value($timeParse   - $timeBegin,   $ms);

$memBefore->convert($kb);
$memAfter->convert($kb);
$memGlobal->convert($kb);
$memCompute->convert($kb);
$memParse->convert($kb);

print "Statistic:\n";
print "Memory spent before starting:\t\t "                      . $memBefore  . "\n";
print "Memory spent after unset:\t\t "                          . $memAfter   . "\n";
print "Time/memory spent parsing:\t"    . $timeParse   . " \t " . $memParse   . "\n";
print "Time/memory spent computing:\t"  . $timeCompute . " \t " . $memCompute . "\n";
print "Time/memory global:\t\t"         . $timeGlobal  . " \t " . $memGlobal  . "\n";
