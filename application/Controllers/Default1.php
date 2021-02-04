<?php

namespace App\Controllers;

use system\lib\Controller;

class Default1 extends Controller
{
	public function index()
	{
	
		$data=array("title"=>"标题123456");
		

		$this->db->where('term_id >',22);
		//$this->db->where('create_date >= ',"2020-12-07");
		//$this->db->or_where(array('create_date <'=>"2020-12-02"));
		
		//$this->db->like(array('terms_description'=>"描述","terms_name"=>"test2"));
		//$this->db->or_where_in("term_id",array(18,19));
		
		$data=array(
			array("controller"=>mt_rand(1,100),"method"=>"aa".mt_rand(1,100),"show_name"=>"aa-1-1"),
			array("controller"=>mt_rand(1,100),"method"=>"bb".mt_rand(1,100),"show_name"=>"aa-5-1"),
			array("controller"=>mt_rand(1,100),"method"=>"bb".mt_rand(1,100),"show_name"=>""),
			array("controller"=>mt_rand(1,100),"method"=>"bb".mt_rand(1,100),"show_name"=>"aa-5-1"),
			array("controller"=>mt_rand(1,100),"method"=>"bb".mt_rand(1,100),"show_name"=>"aa-5-1"),
			array("controller"=>mt_rand(1,100),"method"=>"bb".mt_rand(1,100),"show_name"=>"aa-5-1"),
		
		);
		//$r=$this->db->insertBatch("authority",$data);
		//var_dump($r);
		//var_dump( $this->db->get("category"));
		$this->view($data);
	}


}
