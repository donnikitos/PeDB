<?php
// Build query
if(count($query) == 0) {
	$output = $this->data;
	$keys = array_keys($this->data);
}
else {
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
				if(!isset($current[$matcherKey]))
					return false;

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
}

// Output items
return $output;
