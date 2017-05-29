<?php

class Domain_Common {

	public static function checkEmail($email) {
		$check = "/^[0-9a-zA-Z_-]+@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+){0,3}$/";

		if (preg_match($check, $email)) {
			return true;
		} else {
			return false;
		}
	}
}
