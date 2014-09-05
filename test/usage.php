<?php


require_once 'bootstrap.php';

use Fobia\Test\ObjectCollectionTest;

$list = ObjectCollectionTest::createListItems(50);

function getExecutionTime($end = null, $start = null)
{
    $start = ($start !== null) ? $start : $_ENV['TIME_START']  ;
    $end   = ($end   !== null) ? $end : microtime(true) ;
    return $end - $start;
}

function resourceUsage()
{
    $ms = getExecutionTime();
    return sprintf('Memory usage: %4.2fMB (peak: %4.2fMB), time: %6.4f',
            memory_get_usage() / 1048576,
            memory_get_peak_usage(true) / 1048576,
            $ms);
}

$_ENV['TIME_START'] = microtime(true);

$collection = new Fobia\ObjectCollection($list);
$collection->set('type', 'edit');

print_r($collection);

echo resourceUsage();