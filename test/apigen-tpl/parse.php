<?php


$file = @$_SERVER['argv'][1];

if (is_readable($file)) {
    $file = realpath($file);
}

print_r($file);

$text = file_get_contents($file);

$replace = array(
    '#<br ?/?>#' => '     ',
    '/<[^>]+>/' => '',
    '/&gt;/' => '>',
    '/&lt;/' => '<',
    '/&#64;/' => '@',
);

echo  preg_replace(array_keys($replace), array_values($replace), $text);