<?php
require('./src/Datastore.php');
try {
	$store = new peDB\Datastore('./test.db');
	$store->loadDatabase();

	// var_dump($keys);
	$res = $store->find([], null, $keys);
	foreach($res as $itm) {
		var_dump($itm);
		print('<br /><br />');
	}

	print('<br /><br />');
	// $store->update(['_id' => 'd325fc8acb861142'], ['wow' => 'nope'], ['upsert' => true]);
	// $store->remove(['wow' => 'yeah'], ['multi' => true]);

	$res = $store->find([], null, $keys);
	foreach($res as $itm) {
		var_dump($itm);
		print('<br /><br />');
	}

	print('<br /><br />');
	print('<br /><br />');
	var_dump($store->data);
} catch(Exception $e) {
	die($e->getMessage());
}
