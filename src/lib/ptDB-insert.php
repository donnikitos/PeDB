<?php
if($this->config['filename'])
	$store = fopen($this->config['filename'].'~', $fOp.'b');

foreach($data as $entry) {
	if(!isset($entry['_id']))
		$entry['_id'] = substr(uniqid(bin2hex(openssl_random_pseudo_bytes(10))), -16);

	if($fOp == 'a+')
		$this->data[] = $entry;

	if($store) {
		fseek($store, -1, SEEK_END);
		if(ftell($store) > 0 && fread($store, 1) != PHP_EOL)
			$addNewline = true;

		$entry = json_encode($entry);
		if(is_callable($this->config['afterSerialization']))
			$entry = $this->config['afterSerialization']($entry);

		fwrite($store, (isset($addNewline) ? PHP_EOL : '').$entry);
	}
}

if($store)
	fclose($store);

// Output items
$output = count($data);
if(is_callable($callback))
	$callback(null, $output);

return $output;
