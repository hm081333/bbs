<?php

class Api_Common extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'checkEmail' => array(
				'email' => array('name' => 'email', 'type' => 'string', 'require' => false, 'desc' => 'Email'),
			),
		);
	}

	public function checkEmail()
	{
		$check = "/^[0-9a-zA-Z_-]+@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+){0,3}$/";

		if (preg_match($check, $this->email)) {
			return true;
		} else {
			return false;
		}
	}

}
