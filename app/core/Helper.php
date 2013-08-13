<?php 
/**
 * file: Helper.php
 * @author Alexander Rüedlinger
 *
 */
namespace app\core;
class Helper {
	
	private static $days = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
	
	
	public static function isDay($day){
		foreach(Helper::$days as $d){
			if(strcmp($d,$day)==0)
				return true;
		}
		return false;
	}
	
	public static function formatDayString($day){
		return ucfirst(strtolower($day));
	}
	
	public static function  utf8_string_array_encode(&$array){
		$func = function(&$value,&$key){
			if(is_string($value)){
				$value = utf8_encode($value);
			}
			if(is_string($key)){
				$key = utf8_encode($key);
			}
			if(is_array($value)){
				Helper::utf8_string_array_encode($value);
			}
		};
		array_walk($array,$func);
		return $array;
	}
	
	public static function  utf8_string_array_decode(&$array){
		$func = function(&$value,&$key){
			if(is_string($value)){
				$value = utf8_decode($value);
			}
			if(is_string($key)){
				$key = utf8_decode($key);
			}
			if(is_array($value)){
				Helper::utf8_string_array_decode($value);
			}
		};
		array_walk($array,$func);
		return $array;
	}
	
	public static function getParamNames($path){
		$slices = explode('/',$path);
		$filter = function($slice){
			return !empty($slice) && strcmp($slice[0],':')==0; //compare first charachter of $slice with ':'
		};
		$paramNames = array_filter($slices,$filter);
		$map = function($param){
			return substr($param,1);
		};
		$paramNames = array_map($map, $paramNames);
		return $paramNames;
	}
	
	/**
	 * 
	 * Source: http://www.daveperrett.com/articles/2008/03/11/format-json-with-php/
	 * 
	 * 
	 * Indents a flat JSON string to make it more human-readable.
	 *
	 * @param string $json The original JSON string to process.
	 *
	 * @return string Indented version of the original JSON string.
	 */
	public function json_pretty_string($json) {
		$result = '';
		$pos = 0;
		$strLen = strlen ( $json );
		$indentStr = '  ';
		$newLine = "\n";
		$prevChar = '';
		$outOfQuotes = true;
		
		for($i = 0; $i <= $strLen; $i ++) {
			
			// Grab the next character in the string.
			$char = substr ( $json, $i, 1 );
			
			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = ! $outOfQuotes;
				
				// If this character is the end of an element,
				// output a new line and indent the next line.
			} else if (($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for($j = 0; $j < $pos; $j ++) {
					$result .= $indentStr;
				}
			}
			
			// Add the character to the result string.
			$result .= $char;
			
			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				
				for($j = 0; $j < $pos; $j ++) {
					$result .= $indentStr;
				}
			}
			
			$prevChar = $char;
		}
		
		return $result;
	}
}
?>