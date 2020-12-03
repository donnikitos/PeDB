<?php
require('./src/Datastore.php');
try {
	$store = new ptDB\Datastore('./test.db');
	$store->loadDatabase();

	$res = $store->find(['index' => ['$gt' => '0']], null, $keys);
	var_dump($keys);
	foreach($res as $itm) {
		var_dump($itm['_id']);
		print('<br /><br />');
	}
	// $store->insert(['test' => 'cool']);
} catch(Exception $e) {
	die($e->getMessage());
}
