<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$oc = new Fobia\ObjectCollection();

//var_dump($oc);


//$dsn = 'mysql:dbname=test2;host=127.0.0.1';
//$user = 'root';
//$password = '';
//
//$dbh = new PDO($dsn, $user, $password);
//var_dump($dbh);


require_once dirname(__FILE__) . '/../../../dj-store.loc/dj-store.ru/www/req/inc/admin/standalone.inc.php';

$order = new model_order(16021);

//dump($order);

$collection = $order->products();
// $col//lection->
//echo $collection->count();
foreach($collection as $item) {
    $oc->addAt($item);
}

dump($oc->count());

//
//
$oc->sort(function($a, $b) {
        if ($a->order_group_id->get() == $b->order_group_id->get()) {
            return 0;
        }
        return ($a->order_group_id->get() < $b->order_group_id->get()) ? 1 : -1;
});

$arr = array();
$oc->each(function($obj) use(&$arr) {
    // echo $obj->order_group_id() . PHP_EOL;
    $arr[] =  $obj->order_group_id();
});
$arr = array_unique($arr);
//$arr = $oc->call('order_group_id');
dump($arr);