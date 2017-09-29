<?php

class Model_Tieba extends PhalApi_Model_NotORM
{
	private $baiduid = 'baiduid';

	public function getTiebasByJoin($limit, $offset, $where, $field = 't.*', $order = 't.id desc')
	{
		$name = $this->getTableName(0);
		$condition = DI()->tool->parseSearchWhere($where, true);
		$sql = "SELECT {$field} FROM {$this->prefix}{$name} AS t LEFT JOIN {$this->prefix}{$this->baiduid} AS b ON t.baidu_id=b.id {$condition['sql']} ORDER BY {$order} LIMIT $offset, $limit";
		$params = $condition['params'];
		return $this->getORM()->queryRows($sql, $params);
	}

	public function getTiebasByJoinCount($where)
	{
		$name = $this->getTableName(0);
		$condition = DI()->tool->parseSearchWhere($where, true);
		$sql = "SELECT COUNT(*) AS c FROM {$this->prefix}{$name} AS t LEFT JOIN {$this->prefix}{$this->baiduid} AS b ON t.baidu_id=b.id {$condition['sql']}";
		$params = $condition['params'];
		return $this->getORM()->queryRows($sql, $params);
	}

	protected function getTableName($id)
	{
		return 'tieba';
	}
}
