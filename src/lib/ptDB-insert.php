<?php
if($this->config['filename'])
	$store = fopen($this->config['filename'].'~', 'a+b');

foreach($data as $entry) {
	$this->data[] = $entry;

	if($store) {
		fseek($store, -1, SEEK_END);
		if(fread($store, 1) != PHP_EOL)
			$addNewline = true;

		$entry = json_encode($entry);
		if(is_callable($this->config['afterSerialization']))
			$entry = $this->config['afterSerialization']($entry);

		fwrite($store, ($addNewline ? PHP_EOL : '').$entry);
	}
}

if($store)
	fclose($store);

// Output items
if(is_callable($callback))
	$callback(null, $output);

return $output;
