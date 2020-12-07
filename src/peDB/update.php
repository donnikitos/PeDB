<?php
$data = $this->find($query, $keys);

foreach($keys as $key)
	unset($this->data[$key]);

if($options['upsert'] && count($data) == 0)
	$data[] = [];

foreach($data as &$entry) {
	$op = 'set';
	if(substr(key($update), 0, 1) == '$') {
		$op = substr(key($update), 1);
		$update = $update['$'.$op];
	}

	$updateKeys = explode('.', key($update));

	switch($op) {
		case 'set':
			$val = $update[key($update)];
			foreach($updateKeys as $key)
				$val = [$key => $val];
			$entry = array_merge($entry, $val);
			break;
		case 'unset':
		case 'inc':
		case 'min':
		case 'max':
		case 'push':
		case 'pop':
		case 'addToSet':
		case 'pull':
		case 'each':
		case 'slice':
	}

	$this->data[] = $entry;

	if(!$options['multi'])
		break;
}

$data = $this->data;
$fOp = 'w+';
return include('insert.php');
