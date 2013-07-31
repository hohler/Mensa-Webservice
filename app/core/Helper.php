<?php 
namespace app\core;
class Helper {
	public static function  utf8_string_array_encode(&$array){
		$func = function(&$value,&$key){
			if(is_string($value)){
				$value = utf8_encode($value);
			}
			if(is_string($key)){
				$key = utf8_encode($key);
			}
			if(is_array($value)){
				self::utf8_string_array_encode($value);
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
				self::utf8_string_array_decode($value);
			}
		};
		array_walk($array,$func);
		return $array;
	}
}
?>