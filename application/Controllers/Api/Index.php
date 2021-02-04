<?php

namespace app\controllers\api;

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



class Index
{


	public function index()
	{
		
		//$data =	array("statusCode" => "200");
		//$data['message'] = '使用方式 域名/api/v1/资源/id ，如:www.xxx.com/api/v1/post';
		return view($this->viewDir());
	}

	
}
