<?php

namespace app\Controllers\admin;

use app\controllers\admin\AdminBase;


class Index extends AdminBase

{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{

		$data["data"] = "數據";
		$data["title"] = "后台首页 ";
		$this->view($data);	
	}

	//--------------------------------------------------------------------

}
