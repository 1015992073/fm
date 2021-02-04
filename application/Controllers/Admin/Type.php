<?php

namespace app\controllers\admin;

use app\controllers\admin\AdminBase;

class Type extends AdminBase
{


	protected $tableName = "Type";

	public function __construct(...$params)
	{
		parent::__construct(...$params);
	

		$modelFields = [
			//type：类型，default:默认值,multiple:是否是多语,option:选项值 ,如果支持多语言，它应该是一个二维数组， ,listshow:在列表的时候显示显示
			'type_id' => array('type' => "hidden", 'default' => "", 'multiple' => false, "option" => [], "listshow" => true),
			'type_name' => array('type' => "text", 'default' => "", 'multiple' => true, "option" => [], "listshow" => true),
			'type_slug' => array('type' => "text", 'default' => "", 'multiple' => false, "option" => [], "listshow" => true),
			'type_description' => array('type' => "textarea", 'default' => "", 'multiple' => true, "option" => [], "listshow" => false),
			'status' => array('type' => "radio", 'default' => 1, 'multiple' => false, "option" =>  ['0'=>lang("disabled"),'1'=>lang("enable")], "listshow" => true),
			'sort' => array('type' => "text", 'default' => 0, 'multiple' => false, "option" => [], "listshow" => true),
		];
		$this->data["fields"] = $modelFields; //
		$this->data["primary_key"] = 'type_id'; //表主键 ，必须设置
		$this->data["controller"] = 'type'; ////控制器链接，不带admin,但是带层级（路径），必须设置

	}

	
}
