<?php

class Domain_Common
{

	public static function checkEmail($email)
	{
		$check = "/^[0-9a-zA-Z_-]+@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+){0,3}$/";

		if (preg_match($check, $email)) {
			return true;
		} else {
			return false;
		}
	}

	public static function hash($password, $algo = PASSWORD_DEFAULT, $options = null)
	{
		if (empty($options)) {
			return password_hash($password, $algo);
		} else {
			return password_hash($password, $algo , $options);
		}
	}

	public static function verify($password, $hash)
	{
		return password_verify($password, $hash);
	}
}
