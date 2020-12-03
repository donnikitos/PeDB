<?php
// Build query
$q = [];
foreach($query as $key => $val) {
	$tmp = [
		'keys' => explode('.', $key),
		'op' => 'eq',
		'match' => $val
	];
	if(is_array($val)) {
		$tmp['op'] = substr(key($val), 1);
		$tmp['match'] = current($val);
	}

	$q[] = $tmp;
}

// Filter items
$output = array_filter($this->data, function($entry, $key) use (&$keys, $q) {
	foreach($q as $matcher) {
		$current = $entry;
		foreach($matcher['keys'] as $matcherKey) {
			$current = $current[$matcherKey];
			// if(is_array($current))
			// 	break;
		}

		if(compare($current, $matcher['op'], $matcher['match'])) {
			$keys[] = $key;

			return true;
		}
		return false;
	}
}, ARRAY_FILTER_USE_BOTH);

// Output items
if(is_callable($callback))
	$callback(null, $output);

return $output;
