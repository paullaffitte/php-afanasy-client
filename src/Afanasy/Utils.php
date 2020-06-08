<?php

namespace Afanasy;

class Utils {
	static function checkRegExp($pattern) {
		try {
			preg_match("/{$pattern}/", "dummy");
		} catch (Exception $e) {  # TODO: Too broad exception clause
			print("Error: Invalid regular expression pattern {$pattern}\n");
			print_r($e);
			return false;
		}
		return true;
	}
}
