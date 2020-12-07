<?php
namespace peDB;
	require('peDB/_fn.php');

class Datastore {
	protected $config = [
			// path to the file where the data is persisted. If left blank, the datastore is automatically considered in-memory only.
			// It cannot end with a ~ which is used in the temporary files NeDB uses to perform crash-safe writes
		'filename' => null,
			// as the name implies
		'inMemoryOnly' => false,
			// timestamp the insertion and last update of all documents, with the fields createdAt and updatedAt. User-specified values
			// override automatic generation, usually useful for testing
		'timestampData' => false,
			// if used, the database will automatically be loaded from the datafile upon creation (you don't need to call loadDatabase).
			// Any command issued before load is finished is buffered and will be executed when load is done
		'autoload' => false,
			// if you use autoloading, this is the handler called after the loadDatabase. It takes one error argument. If you use autoloading
			// without specifying this handler, and an error happens during load, an error will be thrown
		'onload' => null,
			// hook you can use to transform data after it was serialized and before it is written to disk. Can be used for example to
			// encrypt data before writing database to disk. This function takes a string as parameter (one line of an NeDB data file) and
			// outputs the transformed string, which must absolutely not contain a \n character (or data will be lost)
		'afterSerialization' => null,
			// inverse of afterSerialization. Make sure to include both and not just one or you risk data loss. For the same reason, make
			// sure both functions are inverses of one another. Some failsafe mechanisms are in place to prevent data loss if you misuse the
			// serialization hooks: NeDB checks that never one is declared without the other, and checks that they are reverse of one another
			// by testing on random strings of various lengths. In addition, if too much data is detected as corrupt, NeDB will refuse to start
			// as it could mean you're not using the deserialization hook corresponding to the serialization hook used before (see below)
		'beforeDeserialization' => null,
			// between 0 and 1, defaults to 10%. NeDB will refuse to start if more than this percentage of the datafile is corrupt. 0 means you
			// don't tolerate any corruption, 1 means you don't care
		'corruptAlertThreshold' => 0.1,
			// function compareStrings(a, b) compares strings a and b and return -1, 0 or 1. If specified, it overrides default string comparison
			// which is not well adapted to non-US characters in particular accented letters. Native localCompare will most of the time be the right choice
		'compareStrings' => 'strcmp'
	];
	public $data;
	// protected $data;


	public function __construct($options = []) {
		if(is_array($options)) {
			$this->config = array_merge($this->config, $options);
		}
		elseif(is_string($options)) {
			$this->config['filename'] = $options;
		}

		foreach($this->config as $key => $value) {
			switch($key) {
				case 'filename':
					if($value && $value != '') {
						$value = realpath($value);
						if(is_dir($value))
							throw new \Exception('ptDB - can not initialise a Datastore, filename points to a directory');
						// if(!is_writable($value))
						// 	throw new \Exception('ptDB - can not initialise a Datastore, filename is not writable');

						$this->config['filename'] = $value;
						$this->config['inMemoryOnly'] = false;
					}
					else {
						$this->config['inMemoryOnly'] = true;
					}
					break;
				case 'inMemoryOnly':
					if($value === true)
						$this->config['filename'] = null;
					break;
				case 'onload':
					if($value && !is_callable($value))
						throw new \Exception('ptDB - can not initialise a Datastore with a faulty onload method');
					break;
				case 'afterSerialization':
					if(is_callable($value) && !is_callable($this->config['beforeDeserialization']))
						throw new \Exception('ptDB - can not initialise a Datastore with a serialization menthon and without a deserialization method');
					break;
				case 'beforeDeserialization':
					if(is_callable($value) && !is_callable($this->config['afterSerialization']))
						throw new \Exception('ptDB - can not initialise a Datastore with a deserialization menthon and without a serialization method');
					break;
				case 'compareStrings':
					if(!is_callable($value))
						throw new \Exception('ptDB - can not initialise a Datastore with a corrupt compareStrings function');
					break;
			}
		}

		if($this->config['autoload'] === true)
			$this->loadDatabase();
	}

	public function loadDatabase() {
		if(is_array($this->data))
			return;

		$this->data = [];

		if($this->config['filename']) {
			$store = fopen($this->config['filename'], 'a+b');
			copy($this->config['filename'], $this->config['filename'].'~');
			rewind($store);

			while(($line = fgets($store)) !== false) {
				$line = trim($line);
				if(strlen($line) < 1)
					continue;

				if(is_callable($this->config['beforeDeserialization']))
					$line = $this->config['beforeDeserialization']($line);

				$this->data[] = json_decode($line, true);
			}

			fclose($store);
		}
	}

	public function __call($name, $args) {
		if(!is_array($this->data))
			return;

		if($name == 'findOne') {
			return $this->find([]);
		}
		elseif($name == 'count') {
			return $this->find([]);
		}
	}

	public function find($query, &$keys = []) {
		if(!is_array($this->data))
			return;

		if(!is_array($keys))
			$keys = [];
		return include('peDB/find.php');
	}

	public function insert($data) {
		if(!is_array($this->data))
			return;

		if(count(array_filter(array_keys($data), 'is_string')) > 0)
			$data = [$data];

		$fOp = 'a+';
		return include('peDB/insert.php');
	}

	public function update($query, $update, $options = []) {
		if(!is_array($this->data))
			return;

		$options = array_merge([
			'multi' => false,
			'upsert' => false,
			'returnUpdatedDocs' => false
		], $options);

		return include('peDB/update.php');
	}

	public function remove($query, $options = []) {
		if(!is_array($this->data))
			return;

		$options = array_merge([
			'multi' => false
		], $options);

		return include('peDB/remove.php');
	}

	public function __destruct() {
		rename($this->config['filename'].'~', $this->config['filename']);
	}
}
