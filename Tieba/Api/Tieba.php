<?php

/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Api_Tieba extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'addBdussAC' => array(
				'bduss' => array('name' => 'bduss', 'type' => 'string', 'require' => true, 'desc' => 'BDUSS'),
			),
			'tiebaList' => array(),
			'refreshTieba' => array(
				'baidu_id' => array('name' => 'baidu_id', 'type' => 'int', 'require' => true, 'desc' => 'baiduid表的ID'),
			),
			'deleteTieba' => array(
				'tieba_id' => array('name' => 'tieba_id', 'type' => 'int', 'require' => true, 'desc' => 'tieba表的ID'),
			),
			'noSignTieba' => array(
				'tieba_id' => array('name' => 'tieba_id', 'type' => 'int', 'require' => true, 'desc' => 'tieba表的ID'),
				'no' => array('name' => 'no', 'type' => 'boolean', 'require' => true, 'desc' => '是否忽略签到'),
			),
			'doSignAll' => array(),
			'doSignByBaiduId' => array(
				'baidu_id' => array('name' => 'baidu_id', 'type' => 'int', 'require' => true, 'desc' => 'baiduid表的ID--签到该bduss所有贴吧'),
			),
			'doSignByUserId' => array(
				'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '会员的ID--签到会员所有贴吧'),
			),
			'doSignByTiebaId' => array(
				'tieba_id' => array('name' => 'tieba_id', 'type' => 'int', 'require' => true, 'desc' => '贴吧的ID--单独签到一个吧'),
			),
		);
	}

	public function addBduss()
	{
		DI()->view->show('add_bduss');
	}

	public function addBdussAC()
	{
		$user_id = $_SESSION['user_id'];
		$rs = Domain_Tieba::addBduss($user_id, $this->bduss);
		if (is_string($rs)) {
			throw new PhalApi_Exception($rs);
		}
		DI()->response->setMsg($rs['msg']);
	}

	public function tiebaList()
	{
		$user_id = $_SESSION['user_id'];
		$baiduid_model = new Model_BaiduId();
		$tieba_model = new Model_Tieba();
		$baiduids = $baiduid_model->getListByWhere(array('user_id' => $user_id), 'id, name', 'id asc');
		$tiebas = array();
		foreach ($baiduids as $baiduid) {
			//$tiebas[$baiduid['id']] = array();
			$tiebas[$baiduid['id']]['name'] = $baiduid['name'];
			$tiebas[$baiduid['id']]['tieba'] = $tieba_model->getListByWhere(array('user_id' => $user_id, 'baidu_id' => $baiduid['id']), '*', 'id asc');
		}
		//$tieba_list = $tieba_model->getList((($this->page - 1) * each_page), ($this->page * each_page));
		//$tieba_list['page_total'] = ceil($tieba_list['total'] / each_page);
		//DI()->view->assign(array('rows' => $tieba_list['rows'], 'total' => $tieba_list['total'], 'page' => $this->page));
		DI()->view->assign(array('tiebas' => $tiebas));
		DI()->view->show('tieba_list');
	}

	public function refreshTieba()
	{
		$result = Domain_Tieba::scanTiebaByPid($this->baidu_id);
		DI()->response->setMsg(T('刷新成功'));
		return true;
	}

	public function refreshAllTieba()
	{
		$user_id = $_SESSION['user_id'];
		Domain_Tieba::scanTiebaByUser($user_id);
		DI()->response->setMsg(T('刷新成功'));
		return true;
	}

	public function deleteTieba()
	{
		//$tieba_id = $this->tieba_id;
		Domain_Tieba::deleteTieba($this->tieba_id);
		DI()->response->setMsg(T('删除成功'));
	}

	public function noSignTieba()
	{
		$no = intval($this->no);
		Domain_Tieba::noSignTieba($this->tieba_id, $no);
		DI()->response->setMsg(T('操作成功'));
	}

	public function doSignAll()
	{
		Domain_Tieba::doSignAll();
		DI()->response->setMsg(T('签到成功'));
	}

	public function doSignByBaiduId()
	{
		Domain_Tieba::doSignByBaiduId($this->baidu_id);
		DI()->response->setMsg(T('签到成功'));
	}

	public function doSignByUserId()
	{
		Domain_Tieba::doSignByUserId($this->user_id);
		DI()->response->setMsg(T('签到成功'));
	}

	public function doSignByTiebaId()
	{
		Domain_Tieba::doSignByTiebaId($this->tieba_id);
		DI()->response->setMsg(T('签到成功'));
	}

}
