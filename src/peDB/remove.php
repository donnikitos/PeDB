<?php
$data = $this->find($query, $keys);

foreach($keys as $key) {
	unset($this->data[$key]);

	if(!$options['multi'])
		break;
}

$data = $this->data;
$fOp = 'w+';
return include('insert.php');
