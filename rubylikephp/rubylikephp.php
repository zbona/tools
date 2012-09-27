<?php
/**
* This library was written just for fun.
* You can use common datatypes in PHP 
* like object types in Ruby language.
* I've called this object as Extendend Data Object (EDO)
*
* EDO = Extended Data Object for php
*
* Methods for different datatypes have the same name 
* /for example length for arrays and also for strings
* Some are called according to ruby, some by different way
* but method names are very short for fast using.
*
* Each method returns an object itselves so
* it can be used method chaining for faster writting
*
* LOOK OUT - this library causes that the final script
* is slower and more dificult for processing.
* It's not recommanded to use it in any systems.
* It's good just for playing or writing personal
* basic scripts for faster writting.
*
* For help with using read example.php
*
* AUTHOR: zbynek.petr@gmail.com
*/

// common function for EDO creation
if (function_exists('edo')) {
	echo "RubyLikePHP error - function called edo() already exists!!!";
} else {
	function edo($var) {
		global $smart_db_65413215648754;
		if (empty($smart_db_65413215648754)) {
			$smart_db_65413215648754 = new RubyLikePHP($var);
		} else {
			$smart_db_65413215648754->set_variable($var);
		}
		return $smart_db_65413215648754;
	}
}

/**
* Class RubyLikePHP for using PHP like Ruby
* new object is handling it's data, data_type
* and operations in the object way.
*/
class RubyLikePHP {

	public static $counter = 0;
	private $variable;
	private $variable_type;
	private $filename;	
	private $errors;

	function __construct($var) {
		self::$counter++;
		$this->errors = array();
		$this->set_variable($var);
	}

	/**
	* Sets extended data object (EDO)
	* for processing next operations
	*/
	function set_variable($var) {
		if (empty($var)) {
			$var = " ";
		}
		$this->variable = $var;
		if (is_array($var)) {
			$this->variable_type = 'array';
		} else {
			if (is_object($var)) {
				$this->variable_type = 'object';
			} else 
			if (is_numeric($var)) {
				$this->variable_type = 'int';
			} else
			if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $var)) {
				$this->variable_type = 'date';
			} else
			if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $var)) {
				$this->variable_type = 'datetime';
			} else {
				$this->variable_type = 'string';
			}
		}
	}

	/**
	* Returns pure value of the EDO back
	*/
	function v() {
		return $this->variable;
	}

	/**
	* Returns string value of the EDO according to it's type.
	*/
	function to_s($param = null) {
		switch ($this->variable_type) {
			case 'date':
				if ($param == null) $param = "d.m.Y";
				return date($param, strtotime($this->variable));
			break;
			case 'datetime':
				if ($param == null) $param = "d.m.Y H:i";
				return date($param, strtotime($this->variable));
			break;
			case 'string':
				return $this->variable;
			break;
			default:
				return var_export($this->variable, true);
			break;
		}
	}


	/**
	* Echo current value of EDO
	*/
	function e() {
		echo $this->to_s();
	}

	/**
	* Shortcut for array_map function (for array EDO)
	*/
	function each_map($param, $body) {
		if ($this->variable_type == 'array') {
			if (!empty($body)) {
				$fnc = create_function($param, $body);
			} else {
				$fnc = $param;
			}
			if ($fnc) {
				foreach ($this->variable as $key => $val) {
					$this->variable[$key] = array_map($fnc, $val);
				}
			} else {
				return $this->error_handler('each_map');
			}
			return $this;
		}

	}

	/**
	* Shortcut for array_map function (for array EDO)
	*/
	function map($param, $body = "") {
		if ($this->variable_type == 'array') {
			if (!empty($body)) {
				$fnc = create_function($param, $body);
			} else {
				$fnc = $param;
			}
			if ($fnc) {
				$this->variable = array_map($fnc, $this->variable);
			} else {
				return $this->error_handler('map');
			}
			return $this;
		}

		echo 'No method map() on type ' . $this->variable_type;
		return false;
	}


	/**
	* Not implemented yet.
	*/
	function each($block) {
		
	}


	/**
	* Prints debug content of current variable of EDO
	*/
	function p() {
		echo '<pre>';
		var_export($this->variable);
		echo '</pre>';
		return $this->variable;
	}

	/** 
	* Returns kes of the array in EDO
	*/
	function keys() {
		if ($this->variable_type == 'array') {
			$this->variable = array_keys($this->variable);
			return $this;
		} else {
			return $this->error_handler('add');
		}
	}

	/** 
	* replace substrings in the strings in the array in EDO
	*/
	function each_replace($a, $b) {

		if ($this->variable_type == 'array') {
			foreach ($this->variable as $key => $val) {
				if (is_string($val)) {
					$this->variable[$key] = str_replace($a, $b, $val);
				}
			}
			return $this;
		} else {
			return $this->error_handler('replace');
		}

	}

	/** 
	* replace substrings in the string (in EDO)
	*/
	function replace($a, $b) {

		if ($this->variable_type == 'string') {
			$this->variable = str_replace($a, $b, $this->variable);
			return $this;
		} else {
			return $this->error_handler('replace');
		}

	}

	/**
	* Filter an array
	*/
	function filter($param = null, $body = null) {
		if ($this->variable_type == 'array') {
			if ($param == null) {
				$this->variable = array_filter($this->variable);
				return $this;
			} else {
				$fnc = create_function($param, $body);
				if ($fnc) {
					$this->variable = array_filter($this->variable, $fnc);
				} else {
					echo 'filter(): Function declaration problem: ' . $body;
					return false;
				}
				return $this;
			}
		} else {
			return $this->error_handler('filter');
		}
	}

	/**
	* Do reqular expression and return true is string matches the expression
	*/
	function re_($pattern, $flags = 'i') {
		if ($this->variable_type == 'string') {
			$ret = preg_match('/' . $pattern . '/i', $this->variable);
			$ret = $ret == 1 ? true : false;
			return $ret;
		}
		return $this->error_handler('re_');
	}

	/**
	* Do reqular expression and return an array of matches
	*/
	function re($pattern, $flags = 'i') {
		if ($this->variable_type == 'string') {
			preg_match('/' . $pattern . '/i', $this->variable, $matches);
			return edo($matches);
		}
		return $this->error_handler('re');
	}

	/**
	* Returns value on the given index (item in the array, char in the string..)
	*
	*/
	function i($index_from, $len = 1) {
		if ($len != null || $len != 1) {
			$index_to = $index_from + $len -1;
		} else {
			$index_to = null;
		}
		switch ($this->variable_type) {
			case 'array':
				if ($index_to == null) {
					return edo($this->variable[$index_from]);
				}
				else {
					$new_array = array();
					foreach ($this->variable as $k => $v) {
						if ($k >= $index_from && $k <= $index_to) {
							$new_array[$k] = $v;
						}
					}
					$this->variable = $new_array;
					return $this;
				}
			break;
			case 'string':
				if ($index_to == null) {
					return $this->variable[$index];
				} else {
					if ($len == null) {
						$this->variable = substr($this->variable, $index_from);
						return $this;
					} else {
						$this->variable = substr($this->variable, $index_from, $len);
						return $this;
					}
				}
			break;
			default:
				return $this->error_handler('i');
			break;
		}
	}

	/*
	* Not implemented yet.
	*/
	function last() {
	}

	/*
	* returns first (element, char...)
	*/
	function first() {
		$this->i(0);
	}

	function push($item) {
		if ($this->variable_type == 'array') {
			if (is_array($item)) {
				foreach ($item as $k => $v) {
					$this->variable[$k] = $v;
				}
			} else {
				$this->variable[] = $item;
			}
			return $this;
		}
		return $this->error_handler('push');
	}

	/*
	* Not implemented yet.
	*/
	function rm() {
	}

	function each_split($str) {
		if ($this->variable_type == 'array') {
			foreach ($this->variable as $key => $val) {
				$this->variable[$key] = explode($str, $val);	
			}
			return $this;
		}
		return $this->error_handler('split');
	}

	/**
	* Split string to array
	*/
	function split($str) {
		if ($this->variable_type == 'string') {
			$this->variable_type = 'array';
			$this->variable = explode($str, $this->variable);
			return $this;
		}
		return $this->error_handler('split');
	}

	/**
	* join array into the string
	*/
	function join($str) {
		if ($this->variable_type == 'array') {
			$this->variable_type = 'string';
			$this->variable = implode($str, $this->variable);
			return $this;
		}
		return $this->error_handler('join');
	}

	/**
	* uniq the array
	*/
	function uniq() {
		if ($this->variable_type == 'array') {
			$this->variable = array_unique($this->variable);
			return $this;
		}
		return $this->error_handler('uniq');
	}

	/**
	* sort an array
	*/
	function sort() {
		if ($this->variable_type == 'array') {
			$this->variable = sort($this->variable);
			return $this;
		}
		return $this->error_handler('sort');
	}

	/**
	* Reverse the string or array according to object type
	*/
	function reverse() {
		if ($this->variable_type == 'string') {
			$this->variable = strrev($this->variable);
			return $this;
		} else if ($this->variable_type == 'array') {
			$this->variable = array_reverse($this->variable);
			return $this;
		}
		return $this->error_handler('reverse');
	}

	/**
	* Is object is an array, returns max value
	*/
	function max() {
		if ($this->variable_type == 'array') {
			return max($this->variable);
		}
		return $this->error_handler('max');
	}

	/**
	* Is object is an array, returns min value
	*/
	function min() {
		if ($this->variable_type == 'array') {
			return min($this->variable);
		}
		return $this->error_handler('min');
	}

	/**
	* Sum all int values in the array
	*/
	function sum() {
		if ($this->variable_type == 'array') {
			return array_sum($this->variable);
		}
		return $this->error_handler('sum');
	}

	/**
	* upcase the string
	*/
	function upcase() {
		if ($this->variable_type == 'string') {
				$this->variable = (mb_strtoupper($this->variable, "utf-8"));
				return $this;
		}
		return $this->error_handler('upcase');
	}

	/**
	* Downcase the string
	*/
	function downcase() {
		if ($this->variable_type == 'string') {
			$this->variable = (mb_strtolower($this->variable, "utf-8"));
			return $this;
		}
		return $this->error_handler('downcase');
	}

	/**
	* If object type is a string, then returns array of it's lines
	*/
	function lines() {
		if ($this->variable_type == 'string') {
			return $this->split("\n");
		}
		return $this->error_handler('lines');
	}

	/**
	* Filter an array for just some string values
	*/
	function find($param) {
		if ($this->variable_type == 'array') {
			$new_arr = array();
			foreach ($this->variable as $key => $val) {
				if (strpos($val, $param) !== false) {
					$new_arr[$key] = $val;
				}
			}
			$this->variable = $new_arr;
			return $this;
		} else  {
			return $this->error_handler();
		}
	}

	/**
	* Trim function for string or arrays.
	* If called on array object, trims all items in array
	*/
	function trim() {
		if ($this->variable_type == 'string') {
			$this->variable = trim($this->variable);
			return $this;
		} else if ($this->variable_type == 'array') {
			foreach ($this->variable as $key => $val) {
				$this->variable[$key] = trim($val);
			}
			return $this;
		}

		return $this->error_handler('trim');
	}

	/**
	* Alias for length()
	*/
	function size() {
		return $this->length();
	}

	/**
	* Length of the string or array
	*/
	function length() {
		switch($this->variable_type) {
			case 'array':
				return count($this->variable);
			break;
			case 'string':
				return mb_strlen($this->variable, 'utf-8');
			break;
			default:
				return $this->error_handler('length');
			break;
		}
	}

	/**
	* Grep (line UNIX grep) function for strings and arrays
	*/
	function grep() {
		$argv = func_get_args();
		switch($this->variable_type) {
			case 'array':
				if (in_array($argv[0], $this->variable)) {
					return $param;
				} else {
					return false;
				}
			break;
			case 'string':
				$x = false;
				if (!empty($argv)) {
					foreach ($argv as $a) {
						$xx = strpos($this->variable, $a);
						$xx = $xx === false ? false : true;
						$x = $x || $xx;
					}
				}
				if ($x === false) {	
					return false;
				} else {
					return true;
				}
			break;
			default:
				return $this->error_handler('grep');
			break;
		}
	}

	/**
	* Returns bool according to the string starts witch the character
	*/
	function s_with_($char) {
		if ($this->variable_type == 'string') {
			if ($this->variable[0] == $char) {
				return true;
			} else {
				return false;
			}
		}

		return $this->error_handler('s_with_');
	}

	/**
	* Returns bool according to the string ends witch the character
	*/
	function e_with_($char) {
		if ($this->variable_type == 'string') {
			if ($this->variable[strlen($this->variable)-1] == $char) {
				return true;
			} else {
				return false;
			}
		}

		return $this->error_handler('e_with_');
	}

	/**
	* Not implemented yet
	*/
	function strip() {
	}

	/**
	* Not implemented yet
	*/
	function esc() {
	}

	/**
	* Check if array contains only empty values
	*/
	private function empty_array($arr) {
		if ( empty($arr) ) return true;
		foreach ($arr as $val) {
			if (!empty($val)) return false;
		}
	}

	/**
	* Test if object is empty
	* sting - empty string or string containing only spaces
	* array - empty array or array containing only empty values
	*/
	function empty_() {
		switch ($this->variable_type) {
			case 'array':
				if (empty($this->variable)) return true;
				return ($this->empty_array($this->variable));
			break;
			case 'string':
				$new_string = trim($this->variable);
				return $new_string == '';
			break;
			default:
				return empty($this->variable);
			break;
		}
	}

	/**
	* Add method according to object type:
	* array - push new item
	* string - add in the end of the strng another string
	* date or datetime - add time in format for strtotime() function
	*/
	public function add($str) {
		switch ($this->variable_type) {
			case 'array':
				$this->variable[] = $str;
				return $this;
			break;
			case 'string':
				$this->variable .= $str;
				return $this;
			break;
			case 'datetime':
			case 'date':
				$this->variable = date("Y-m-d H:i:s", strtotime($str, strtotime($this->variable)));
				return $this;
			break;
			default:
				return false;
			break;
		}
		return $this->error_handler('add');
	}

	/**
	* Fetch text file content into string object
	*/
	public function from_f($filename = null) {
		if (!empty($this->filename)) {
			$filename = $this->filename;
		}
		if (!empty($filename) && file_exists($filename)) {
			$this->variable = file_get_contents($filename);
			$this->variable_type = 'string';
			$this->filename = $filename;
			return $this;
		} else {
			echo "File not exists!";
			return false;
		}
	}

	/**
	* Method which saves string object to the file
	*/
	public function to_f($filename = null) {
		if (!empty($this->filename)) {
			$filename = $this->filename;
		}
		if (!empty($filename))
			file_put_contents($filename, $this->to_s());
		$this->filename = $filename;
	}

	/**
	* Error handler for case that mathod is not supported for the data tyle.
	*/
	private function error_handler($function_name) {
		echo "RubyLikePHP error: Function {$function_name}() not defined for {$this->variable_type}";
		return false;
	}
}

?>
