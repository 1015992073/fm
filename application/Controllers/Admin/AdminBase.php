<?php

namespace app\controllers\admin;

use system\lib\Controller;

class AdminBase  extends Controller
{

	public function __construct()
	{
		parent::__construct();

		//var_dump($this->router);

	}


	//index
	public function index()
	{
		Header("HTTP/1.1 301 See Other");
		$controllerUrl = $this->router->directory . "/" . $this->router->controller;

		Header("Location: " . get_site_info("baseurl") . $controllerUrl . '/list');
	}
	//列表
	public function list($page = 1, $pagesize = 10)
	{

		$query = ['table' => $this->tableName, "pagesize" => $pagesize, 'page' => $page, 'orderby' => 'sort ASC, create_date DESC']; //构造查询条件
		$query_result = get_table_list($query);

		$this->data["data"] = $query_result["list"]; //列表
		$this->data["query_tol"] = $query_result["total"]; //总数

		$this->data["pagesize"] = $pagesize;
		$this->data["page"] = $page;
		$this->data["title"] = lang(strtolower($this->tableName)) . lang("list");
		return $this->view($this->data);
	}

	//编辑
	public function edit($id = null)
	{

		if (isset($id)) {
			$query = ['table' => $this->tableName, 'where' => array($this->data["primary_key"] => $id), "return" => "list"]; //构造查询条件
			$thedata = get_table_list($query);
			$this->data["data"] = $thedata; //查询结果

		}
		$this->data["title"] = lang('edit') . lang(strtolower($this->tableName));
		return $this->view($this->data);
	}
	//新增
	public function add()
	{

		$this->data["title"] = lang('add') . lang(strtolower($this->tableName));
		return $this->view($this->data);
	}

	//更新
	public function update()
	{

		if (isset($_POST["data"])) {
			$data = $_POST["data"]; //字段数据
			//var_dump($data);
			if (isset($this->tableName)  && is_string($this->tableName) && $this->tableName != '') {
				$table = strtolower($this->tableName);
			
				foreach ($data as $key => $val) {
					//将传递的数组值转为字符串
					if (is_array($val)) {
						$data[$key] = json_encode($val, JSON_UNESCAPED_UNICODE);
					}
				}

				if (isset($data[$this->data["primary_key"]])) {
					//更新
					$where = array($this->data["primary_key"] => $data[$this->data["primary_key"]]);
					$primary_key = $data[$this->data["primary_key"]];

					//unset($data[$this->data["primary_key"]]); //删除primary_key 的值
					$data["update_date"] = date("Y-m-d H:i:s");
					//var_dump($data);
					$result =	$this->db->updateBatch($table ,[$data], $this->data["primary_key"]);
					if ($result) {
						$this->data["data"] = ['status' => 'sucess', 'message' => lang("update") . lang("success"), 'data' => $primary_key];
						$this->data["title"] = lang("update") . lang("success");
					} else {
						$this->data["data"] = ['status' => 'fail', 'message' => lang("update") . lang("fail"), 'data' => $primary_key];
						$this->data["title"] = lang("update") . lang("fail");
						
					}
				} else {
					//新建
					$this->data["title"] = "";
					$result = $this->db->insertBatch($table ,[$data]);
					
					if (isset($result) && is_numeric($result) && $result>0) {
						$this->data["data"] = ['status' => 'sucess', 'message' => lang("add") . lang("success"), 'data' => $result];
						$this->data["title"] = lang("add") . lang("success");
					} else {
						$this->data["data"] = ['status' => 'fail', 'message' => lang("add") . lang("fail"), 'data' => ''];
						$this->data["title"] = lang("add") . lang("fail");
					}
				}
			}
		}

		return $this->view($this->data);
	}
	//删除
	public function del($data = null)
	{

		if (isset($data) && is_array($data) && count($data)>0 ) {
			$id=$data[0];
			if (isset($this->tableName)  && is_string($this->tableName) && $this->tableName != '') {
				$table = strtolower($this->tableName);
			
				
					$result =	$this->db->delete($table,array($this->data["primary_key"] => $id));
					if (isset($result) && $result===true) {
						$this->data["data"] = ['status' => 'sucess', 'message' => lang("del") . lang("success"), 'data' => ''];
						$this->data["title"] = lang("del") . lang("success");
					} else {
						$this->data["data"] = ['status' => 'fail', 'message' => lang("del") . lang("fail"), 'data' => ''];
						$this->data["title"] = lang("del") . lang("fail");
					}
				
			}
		} else {
			$this->data["data"] = ['status' => 'fail', 'message' => 'ID' . lang("err"), 'data' => ''];
			$this->data["title"] = lang("del") . lang("fail");
		}

		return $this->view($this->data);
	}



	//重写视图函数
	protected  function view($data = null, $viewFile = null)
	{

		$ViewDir = WEBPATH . $this->config->SysTemplateRootDirName . DS . $this->config->adminDirName . DS . $this->config->adminTemplateName . DS; //后台模板目录
		$viewFile = get_view_file($ViewDir);

		if (isset($data) && is_array($data)) {
			//如果有数据，并且数据必须是数组 ，将数组拆成下标的变量
			extract($data);
		} else {
			extract(["data" => $data]);
		}
		include_once($ViewDir . "header.php");
		if (file_exists($viewFile)) {
			include_once($viewFile);
		} else {
			$tmplnotfound = "视图模板不存在:" . $this->router->controller . "/" . $this->router->method . ".php";
			include_once($ViewDir . "404.php");
		}

		include_once($ViewDir . "footer.php");
	}
}
