<?php
function compare($var1, $op, $var2) {
	switch ($op) {
		case '=':
		case 'eq':
			return $var1 == $var2;
		case '!=':
		case 'ne':
		case 'nin':
			return $var1 != $var2;
		case '>=':
		case 'gte':
			return $var1 >= $var2;
		case '<=':
		case 'lte':
			return $var1 <= $var2;
		case '>':
		case 'gt':
			return $var1 > $var2;
		case '<':
		case 'lt':
			return $var1 < $var2;
		case 'in':
		case 'exists':
			return in_array($var1, $var2);
		case 'regex':
			return preg_match($var1, $var2);
		default:
			return true;
	}
}
