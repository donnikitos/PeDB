<?php
$data = $this->find($query, $keys);

foreach($keys as $key)
	unset($this->data[$key]);

if($options['upsert'] && count($data) == 0)
	$data[] = [];

foreach($data as $entry) {
	if(substr(key($update), 0, 1) == '$') {
		$op = substr(key($update), 1);
		$update = $update['$'.$op];

		foreach($update as $key => $val) {
			$updateKeys = array_reverse(explode('.', $key));
			foreach($updateKeys as $eKey)
				$val = [$eKey => $val];

			switch($op) {
				case 'set':
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
		}
	}
	else {
		$entry = array_merge($update, ['_id' => $entry['_id']]);
	}

	$this->data[] = $entry;

	if(!$options['multi'])
		break;
}

$data = $this->data;
$fOp = 'w+';
return include('insert.php');
