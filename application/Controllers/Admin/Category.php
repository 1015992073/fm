<?php


namespace app\controllers\admin;

use app\controllers\admin\AdminBase;


class Category extends AdminBase
{


	protected $tableName = "Category";

	public function __construct(...$params)
	{
		parent::__construct(...$params);

		$modelFields = [
			//field:需要编辑或者更新的字段，type：类型，default:默认值,multiple:是否是多语,option:选项值 ,如果支持多语言，它应该是一个二维数组， ,listshow:在列表的时候显示显示 ,class 添加css 的class,
			'term_id' => array('field' => "", 'type' => "hidden", 'default' => "", 'multiple' => false, "option" => [], "listshow" => true),
			'terms_name' => array('field' => "", 'type' => "text", 'default' => "", 'multiple' => true, "option" => [], "listshow" => true),
			'terms_slug' => array('field' => "", 'type' => "text", 'default' => "", 'multiple' => false, "option" => [], "listshow" => true),
			'terms_description' => array('field' => "", 'type' => "textarea", 'default' => "", 'multiple' => true, "option" => [], "listshow" => false),
			'term_parents' => array('field' => "", 'type' => "select", 'default' => 0, 'multiple' => false, "option" => [], "listshow" => false, "class" => "ui-term-parents"),
			'type_id' => array('field' => "", 'type' => "select", 'default' => 0, 'multiple' => false, "option" => [], "listshow" => true),
			'status' => array('field' => "", 'type' => "radio", 'default' => 1, 'multiple' => false, "option" => ['0' => lang("disabled"), '1' => lang("enable")], "listshow" => true),
			'sort' => array('field' => "", 'type' => "text", 'default' => 0, 'multiple' => false, "option" => [], "listshow" => true),
		];
		$this->data["fields"] = $modelFields; //
		$this->data["primary_key"] = 'term_id'; //表主键 ，必须设置
		$this->data["controller"] = 'Category'; ////控制器链接，不带admin,但是带层级，必须设置
	}


	//列表
	public function list($page = 1, $pagesize = 10)
	{
		$this->data["data"] = getTheChildCategory(); //获取树状分类
		$this->data["title"] = lang(strtolower($this->tableName)) . lang("list");
		return $this->view($this->data);
	}
	//新增
	public function add($data = null)
	{


		$this->data["title"] = lang('add') . lang(strtolower($this->tableName));

		$allCategory = getTheChildCategory(); //树状分类
		$pcatlist = $this->getallcategory($allCategory); //父类选项，选择下拉 格式[0=>"分类1"]
		$pcatlist = array("0" => lang("empty")) + $pcatlist; //将顶级分类添加所有分类中
		$this->data["fields"]["term_parents"]["option"] = $pcatlist;
		if (isset($data) && is_array($data) && count($data) > 0 && is_numeric($data[0])) {
			$this->data["fields"]["term_parents"]["default"] = $data[0];
		}

		$typelist = get_table_list(["table" => "type", "orderby" => 'sort ASC', "return" => "list"]); //获取类型
		$options_type = [];
		if (is_array($typelist) && count($typelist) > 0) {
			foreach ($typelist as $item) {
				$options_type[$item["type_id"]] = langval($item["type_name"]);
			}
		}
		$options_type = array("0" => lang("empty")) + $options_type;
		$this->data["fields"]["type_id"]["option"] = $options_type;
		return $this->view($this->data);
	}

	//编辑
	public function edit($id = null)
	{
		if (isset($id)) {
			$query = ['table' => $this->tableName, 'where' => array($this->data["primary_key"] => $id), "return" => "list"]; //构造查询条件
			$thedata = get_table_list($query);
			if (is_array($thedata)) {
				$this->data["data"] = $thedata; //查询结果
				$allCategory = getTheChildCategory(); //树状分类
				$pcatlist = $this->getallcategory($allCategory); //父类选项，选择下拉 格式[0=>"分类1"]
				$pcatlist = array("0" => lang("empty")) + $pcatlist; //将顶级分类添加所有分类中
				$this->data["fields"]["term_parents"]["option"] = $pcatlist;

				$typelist = get_table_list(["table" => "type", "orderby" => 'sort ASC', "return" => "list"]); //获取类型
				$options_type = [];
				if (is_array($typelist) && count($typelist) > 0) {
					foreach ($typelist as $item) {
						$options_type[$item["type_id"]] = langval($item["type_name"]);
					}
				}
				$options_type = array("0" => lang("empty")) + $options_type;
				$this->data["fields"]["type_id"]["option"] = $options_type;
				//var_dump($typelist);


			} else {
				$this->data["data"] = $thedata; //查询结果
			}
		}
		$this->data["title"] = lang('edit') . lang(strtolower($this->tableName));
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
	//递归获取分类列表
	private function getallcategory($list, $flg = "")
	{
		$pcatlist = [];
		if (is_array($list) && count($list) > 0) {
			$f_flg = "┣" . $flg;
			foreach ($list as $item) {
				$pcatlist[$item["term_id"]] = $f_flg . langval($item["terms_name"]);

				if (isset($item["child"]) && is_array($item["child"]) && count($item["child"]) > 0) {
					$pcatlist = $pcatlist + $this->getallcategory($item["child"], $flg . "━");
				}
			}
		}
		return $pcatlist;
	}
}
