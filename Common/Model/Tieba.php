<?php

class Model_Tieba extends PhalApi_Model_NotORM
{


	protected function getTableName($id)
	{
		return 'tieba';
	}
}
