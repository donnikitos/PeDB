<?php
require('./src/Datastore.php');
try {
	$store = new peDB\Datastore('./test.db');
	$store->loadDatabase();

	// var_dump($keys);
	foreach($store->find([]) as $itm) {
		var_dump($itm);
		print('<br /><br />');
	}

	print('<br /><br />');
	// $store->insert(['hello' => 'world']);
	// $store->update(['_id' => '7635fce1a6ac5cab'], ['$set' => ['hello' => ['cool' => 'world']]]);
	// $store->remove(['wow' => 'yeah'], ['multi' => true]);

	foreach($store->find([]) as $itm) {
		var_dump($itm);
		print('<br /><br />');
	}
} catch(Exception $e) {
	die($e->getMessage());
}
