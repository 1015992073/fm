<?php

namespace App\Controllers\api;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */



class v1 
{

/**
 * 使用post方式获取资源
 * api url：/api/v1/
 * post data ：{tablename:"posts","page"：1,"pagesize":10,where:{"post_id":1,"create_date>":"2020-01-01"}}
 */
	public function index($postdata = null)
	{
		// 响应类型  
		//header('Content-Type: application/json; charset=utf8');
		// 指定允许其他域名访问
		header('Access-Control-Allow-Origin:*');
		$data =	array("statusCode" => "200");
		$data['message'] = 'api v1';
		echo   json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	public function get($tableName = null, $id = null)
	{
		// 响应类型  
		header('Content-Type: application/json; charset=utf8');
		// 指定允许其他域名访问
		header('Access-Control-Allow-Origin:*');
		$data["result"] = [];
		if (isset($tableName)) {
			$db      = \Config\Database::connect();
			if ($db->tableExists(strtolower($tableName))) {
				$sql = $db->table($tableName);
				$pagesize = (isset($_GET["num"]) && is_numeric($_GET["num"]) && $_GET["num"] > 0) ? $_GET["num"] : 10;
				$orderby = (isset($_GET["orderby"]) && $_GET["orderby"] != '') ? $_GET["orderby"] : 'sort ASC';
				$where= [];
				//$page = (isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0) ? $_GET["pagesize"] : 1;
				if(isset($_GET["where"]) && $_GET["where"] != ''){

				}
			
				if (isset($id)) {
					$fields = $db->getFieldData($tableName);
					foreach ($fields as $field) {
						if($field->primary_key>0){
							
							$where[$field->name]=$id;
						}	
					}
				} 
				$query = ['table' => $tableName, "pagesize" => $pagesize, 'page' => 1, 'orderby' => $orderby,"where"=>$where]; //构造查询条件
				$result = sqlQuery($query);
				
				if (isset($result) && is_array($result) && count($result)>0) {

					$data["result"] = $result;
					$data["statusCode"] = '000040';
				} else {
					$data["statusCode"] = '000041';
				}
			} else {
				$data =	array("statusCode" => "000070");
			}
		} else {
			$data =	array("statusCode" => "000050");
		}
		$data['message'] = console($data["statusCode"]);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}
