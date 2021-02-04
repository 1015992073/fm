<?php

/**

 *
 * @package    数据库
 *
 * Mysql缓存的配置和使用 https://www.cnblogs.com/applelife/p/11576295.html
 * order by和group by的区别 https://www.cnblogs.com/rgever/p/9335075.html
 * 实例分析10个PHP常见安全问题 https://www.jb51.net/article/164968.htm  
 * 给你100万条数据的一张表，你将如何查询优化？ https://www.cnblogs.com/llzhang123/p/9239682.html
 */

namespace system\lib;

/**
 * Handle system messages and localization.
 *
 * Locale-based, built on top of PHP internationalization.
 *
 * @package lib
 */
class Database
{

	public $conn;
	public $config;
	public $select = "*";
	public $orderby = "";
	public $where_ = "";
	public $limit_ = '';
	public function __construct()
	{
		$this->config = BaseService::config();
	}

	//链接数据库
	public  function connect($mysql = null)
	{
		if (!isset($mysql)) {

			$mysql = $this->config->mysql;
		}


		$conn = mysqli_connect($mysql["hostname"], $mysql["username"], $mysql["password"], $mysql["database"], $mysql["port"]);


		// 检测连接
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		} else {
			//$this->conn = $conn;
			return $conn;
		}
	}
	//关闭数据库链接
	public  function close($conn = null)
	{
		if (isset($conn)) {
			mysqli_close($conn);
		}
	}



	//当前数据库所有表
	public  function list_tables()
	{
		$conn = $this->connect();
		$sql = "select TABLE_NAME,TABLE_COMMENT from information_schema.tables where table_schema='" . $this->config->mysql["database"] . "' and table_type='base table'";
		$result = $this->query($sql);

		return $this->getfetch($result);
	}

	//表是否存在
	public  function table_exists($tablename = null)
	{
		if (isset($tablename)) {
			//$sql = "SHOW TABLES LIKE '" . $tablename . "'";
			$sql = "select * from information_schema.tables where table_name ='" . $tablename . "'";
			$result = $this->query($sql);
			if (!empty($result)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	//表字段
	public  function field_data($tablename = null)
	{
		if (isset($tablename)) {
			$sql = "DESC " . $tablename;
			$result = $this->query($sql);
			return $this->getfetch($result);;
		} else {
			return [];
		}
	}
	//转` ，并没有使用
	public  function add_flg($string = null)
	{
		$orderstr = '';
		$str_array = explode(",", $string);
		foreach ($str_array as $item) {
			$item = preg_replace("/\s(?=\s)/", "\\1", $item); //将多个空格变成一个
			$itemarray = explode(" ", $item);
			if (count($itemarray) == 1) {
				$fild = "`" . $itemarray[0] . "`";
			} else if (count($itemarray) == 2) {
				$fild = "`" . $itemarray[0] . "` " . $itemarray[1];
			}
			if (strlen($orderstr) > 0) {
				$orderstr .= ',' . $fild;
			} else {
				$orderstr = $fild;
			}
		} //end foreach
		return $orderstr;
	}




	/**
	 *  表的结构格式化 数据
	 * $tablefield array 表字段 $this->field_data()返回值
	 * $data array 需要格式化的数据 ，数据结构应该和表自动一致，如果数据完全不同于表结构，返回false
	 * $must array| string 必须存在是字段， string:多个用,隔开"id,name" ; array：数组类似array("id","name")
	 */

	public  function format($tablefield = null, $data = null, $must = null)
	{
		if (is_array($tablefield) && count($tablefield) > 0 && is_array($data) && count($data) > 0) {
			$newdata = [];
			$istur = 0;
			if (is_string($must) && $must != "") {
				$mustarray = explode(",", $must);
			} else if (is_array($must)) {
				$mustarray = $must;
			} else {
				$mustarray = []; //必须数组
			}
			foreach ($tablefield as $field) {
				if (isset($data[$field["Field"]])) {
					$newdata[$field["Field"]] = $data[$field["Field"]];
				}
				if (in_array($field["Field"], $mustarray)) {
					$istur++; //如果满足就就+1 ，最后比较$istur 和 $mustarray  数量 ，相同就表示可以
				}
			}
			if (count($newdata) > 0 && (count($mustarray) == $istur)) {
				return $newdata;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	//清空表
	public  function empty_table($tablename = null)
	{
		if (is_string($tablename) && $tablename != "") {
			$sql = "DELETE FROM " . $tablename;
			$result = $this->query($sql);
		} else {
			return false;
		}
	}

	//清空表
	public  function truncate($tablename = null)
	{
		if (is_string($tablename) && $tablename != "") {
			$sql = "TRUNCATE " . $tablename;
			$result = $this->query($sql);
		} else {
			return false;
		}
	}

	/**
	 * 排序 $sort=DESC| ASC（升序） mysql 默认 ASC
	 * 
	 * 1. $this->db->order_by('title', 'DESC');
	 *2. $this->db->order_by('title DESC, name ASC');
	 */
	public  function order_by($string = null, $sort = null)
	{
		if (is_string($string)) {
			$orderstr = '';
			if (is_string($sort) && in_array(strtoupper($sort), ["DESC", "ASC"])) {
				//$sort 有设置，并且是DESC 或者 ASC
				$orderstr = "`" . $string . "` " . $sort;
			} else {
				//忽略第二个参数 $sort
				$orderstr = str_replace("，", ",", $string); //
				$orderstr = preg_replace("/\s(?=\s)/", "\\1", $orderstr); //将多个空格替换为一个

			}
		} else {
			$this->orderby = "";
			return $this;
		}
		if (strlen($this->orderby) > 0) {
			$this->orderby .= ',' . $orderstr;
		} else {
			$this->orderby = $orderstr;
		}
		return $this;
	}
	//选择
	public  function select($string = "*")
	{
		if (is_string($string)) {
			if ($string != "") {

				$this->select = $string;
			} else {
				$string = "*";
			}
		} else if (is_array($string)) {
			$this->select =	implode(",", $string);
		}
		return $this;
	}

	//分页
	public  function limit($page = null, $pagesize = null)
	{
		if (is_int($page) && $page > 0 &&  !is_int($pagesize)) {
			//page 设置，$pagesize 没有设置或者不是整数
			$pagesize = 10;
			$this->limit_ = " LIMIT " . ($page - 1) * $pagesize . ", " . $pagesize;
		} else if (is_int($page) && $page > 0 && is_int($pagesize) && $pagesize > 0) {
			//page 设置，$pagesize 有设置
			$this->limit_ = " LIMIT " . ($page - 1) * $pagesize . ", " . $pagesize;
		} else {
			$this->limit_ = "";
		}
		return $this;
	}

	// where and , or 条件
	public  function where_or_and($where = null, $val = null, $or_and = "AND")
	{
		$where_ = '';
		$or_and = $or_and === "OR" ? "OR" : "AND"; //$or_and 不是AND 就是 OR
		if (is_array($where)) {
			//数组

			foreach ($where as $key => $item) {
				$where_c = '';
				if (strpos($key, "!") !== false || strpos($key, ">") !== false || strpos($key, "<") !== false || strpos($key, "=") !== false) {
					if (is_numeric($item)) {
						$where_c = $key  . $item;
					} else if (is_string($item)) {
						$where_c = $key . "'" . $item . "'";
					}
				} else {
					if (is_numeric($item)) {
						$where_c = $key  . "="  . $item;
					} else if (is_string($item)) {
						$where_c = $key  . "=" . "'" . $item . "'";
					}
				}

				if (strlen($where_) > 0) {
					$where_ .= " " . $or_and . " " . $where_c;
				} else {
					$where_ = $where_c;
				}
			}
		} else if (is_string($where)) {
			//字符串
			if (isset($val) && isset($where)) {
				//有设置第二个参数
				if (strpos($where, "!") !== false || strpos($where, ">") !== false || strpos($where, "<") !== false || strpos($where, "=") !== false) {

					if (is_numeric($val)) {
						$where_ = $where . $val;
					} else if (is_string($val)) {
						$where_ = $where . "'" . $val . "'";
					}
				} else {
					if (is_numeric($val)) {
						$where_ = $where . "=" . $val;
					} else if (is_string($val)) {
						$where_ = $where . "=" . "'" . $val . "'";
					}
				}
			} else {
				//没有设置第二个参数
				$where_ = $where;
			}
		} else {
			return '';
		}

		if (strlen($this->where_) > 0) {
			$this->where_ .= " " . $or_and . " " . $where_;
		} else {
			$this->where_ = $where_;
		}
	}

	//where
	/**1.$this->db->where('name', $name);  // Produces: WHERE name = 'Joe' 
	 * 2.$this->db->where('name !=', $name);$this->db->where('id <', $id); //2条语句组合成: WHERE name != 'Joe' AND id < 45
	 * 3.$this->db->where(array('name' => $name, 'title' => $title, 'status' => $status));//WHERE name = 'Joe' AND title = 'my title' AND status = 1
	 * 4.$this->db->where("name='Joe' AND status='boss' OR status='active'");
	 */
	public  function where($where = null, $val = null)
	{
		$this->where_or_and($where, $val);
		return $this;
	}
	//同 where ，and 換or
	public  function or_where($where = null, $val = null)
	{
		$this->where_or_and($where, $val, "OR");
		return $this;
	}
	//where in or , and
	public  function where_in_or_and($where = null, $val = null, $not_in = "IN", $or_and = "AND")
	{
		$or_and = $or_and === "OR" ? "OR" : "AND"; //$or_and 不是AND 就是 OR
		$not_in = $not_in === "NOT IN" ? "NOT IN" : "IN"; //$not_in 不是NOT IN 就是 IN
		if (is_string($where) && $where != '' && is_null($val)) {
			if (strpos($where, $not_in) &&  strpos($where, '(') > 0 &&  strpos($where, ')') > 0) {
				if (strlen($this->where_) > 0) {
					$this->where_ .= " " . $or_and . " " . $where;
				} else {
					$this->where_ = $where;
				}
			}
		} else if (is_string($where) && $where != '' && is_array($val) && count($val) > 0) {
			$instr = "('" . implode("','", $val) . "')";
			$wherein = $where . " " . $not_in . " " . $instr . "";
			if (strlen($this->where_) > 0) {
				$this->where_ .= " " . $or_and . " " . $wherein;
			} else {
				$this->where_ = $wherein;
			}
		}
	}


	/**
	 *  where_in  多个AND 链接
	 * $this->db->where_in('username',  array('Frank', 'Todd', 'James'));// username IN ('Frank', 'Todd', 'James')
	 * $this->db->where_in('id IN ("1","2",6,8)');
	 */
	public  function where_in($where = null, $val = null)
	{
		$this->where_in_or_and($where, $val);
		return $this;
	}

	/** 同where_in
	 *  where_in  多个 OR 链接
	 * $this->db->where_in('username',  array('Frank', 'Todd', 'James'));
	 * $this->db->where_in('id IN ("1","2",6,8)');
	 */
	public  function or_where_in($where = null, $val = null)
	{
		$this->where_in_or_and($where, $val, "OR");
		return $this;
	}

	/**
	 * 
	 * 同where_in
	 */
	public  function where_not_in($where = null, $val = null)
	{
		$this->where_in_or_and($where, $val, "NOT IN");
		return $this;
	}

	/**
	 *  同or_where_in
	 * 
	 */
	public  function or_where_not_in($where = null, $val = null)
	{
		$this->where_in_or_and($where, $val, "NOT IN", "OR");
	}


	/**
	 * like 搜索 
	 * $position=both(默認，非後面2個都是both) or before or after
	 * 1.$this->db->like('title', 'match');
	 * --如果多次调用该方法，那么多个 WHERE 条件将会使用 AND 连接起来:
	 * --$this->db->like('title', 'match');
	 * --$this->db->like('body', 'match');
	 * 2.$array = array('title' => $match, 'title2' => $match, 'title3' => $match);
	 */
	public  function like($where = null, $val = null, $position = "")
	{
		$where_ = '';

		if (is_array($where)) {
			foreach ($where as $key => $item) {
				$where_c = '';
				if ($position == "before") {
					$where_c = "`" . $key . "` LIKE '%" . $item . "' ESCAPE '!'";
				} else if ($position == "after") {
					$where_c = "`" . $key . "` LIKE '" . $item . "%' ESCAPE '!'";
				} else {
					$where_c = "`" . $key . "` LIKE '%" . $item . "%' ESCAPE '!'";
				}
				if (strlen($where_) > 0) {
					$where_ .= " AND " . $where_c;
				} else {
					$where_ = $where_c;
				}
			} //end if
		} else if (is_string($where)) {
			if (isset($val)  && $val != "" && isset($where)) {
				//有设置第二个参数
				if ($position == "before") {
					$where_ = "`" . $where . "` LIKE '%" . $val . "' ESCAPE '!'";
				} else if ($position == "after") {
					$where_ = "`" . $where . "` LIKE '" . $val . "%' ESCAPE '!'";
				} else {
					$where_ = "`" . $where . "` LIKE '%" . $val . "%' ESCAPE '!'";
				}
			} else {
				//没有设置第二个参数
				$where_ = $where;
			}
		} else {
			return '';
		}

		if (strlen($this->where_) > 0) {
			$this->where_ .= " AND " . $where_;
		} else {
			$this->where_ = $where_;
		}
		return $this;
	}

	//查询字符串语句,单条
	public  function query($sql = null)
	{

		if (isset($sql)) {

			$appbegin = microtime(true);
			$conn = $this->connect();
			mysqli_query($conn, "set names 'UTF8'");

			$result =	mysqli_query($conn, $sql);
			//echo '查詢語句: ' . $sql . "</br>";
			$sqlappend = microtime(true);
			//echo "数据库查询语句用时:" . (($sqlappend - $appbegin) * 1000) . 'ms</br>';
			if (strpos($sql, "INSERT INTO") === 0) {
				if ($result) {
					return mysqli_insert_id($conn);
				} else {
					return 0;
				}
			}
			$this->close($conn);

			$this->select = "*"; //重置选择条件
			$this->orderby = ""; //重置排序
			$this->where_ = ""; //重置
			$this->limit_ = ''; //重置
			$append = microtime(true);
			//echo "处理结果:" . (($append - $sqlappend) * 1000) . 'ms</br>';
			return $result;
		} else {
			return false;
		}
	}
	//返回查询数据
	public  function getfetch($result = null)
	{
		//数据处理，如果很多容易出现性能瓶颈
		//$appbegin = microtime(true);
		if (isset($result) && isset($result->num_rows)) {
			$resultarray = [];
			if ($result->num_rows > 0) {
				// 输出数据	
				while ($row = $result->fetch_assoc()) {
					array_push($resultarray, $row);
				}
			}
			//$sqlappend = microtime(true);
			//echo "处理查询返回结果:" . (($sqlappend - $appbegin) * 1000) . 'ms</br>';
			return $resultarray;
		} else {
			//$sqlappend = microtime(true);
			//echo "处理查询返回结果:" . (($sqlappend - $appbegin) * 1000) . 'ms</br>';
			return $result;
		}
	}


	//查询多条语句
	public  function multi_query($sql = null)
	{

		if (isset($sql)) {

			$appbegin = microtime(true);
			$conn = $this->connect();
			mysqli_query($conn, "set names 'UTF8'");
			// 执行多个 SQL 语句
			//$result =mysqli_multi_query($conn, $sql);
			//return mysqli_store_result($conn);
			$resultlist = [];
			if (mysqli_multi_query($conn, $sql)) {

				$num = 0;
				do {
					// 存储第一个结果集
					$sqldoin = microtime(true);
					$therow = [];
					if ($result = mysqli_store_result($conn)) {
						/*
						while ($row = mysqli_fetch_row($result)) {
							array_push($therow, $row);
							$num++;
							echo "处理数据" . $num . "</br>";
						}
						*/
						while ($row = $result->fetch_assoc()) {
							array_push($therow, $row);
						}

						array_push($resultlist, $therow);
						mysqli_free_result($result);
					}
				} while (mysqli_more_results($conn) && mysqli_next_result($conn));
			}
			//echo '查詢語句: ' . $sql . "</br>";

			$this->close($conn);

			$this->select = "*"; //重置选择条件
			$this->orderby = ""; //重置排序
			$this->where_ = ""; //重置
			$this->limit_ = ''; //重置
			$append = microtime(true);
			echo "多条语句处理加处理结果:" . (($append - $appbegin) * 1000) . 'ms</br>';
			return $resultlist;
		} else {
			return false;
		}
	}

	//查询 $returnnumbe 返回结果带查询总数
	public  function get($tablename = null,  $returnnumber = false)
	{

		if (is_string($tablename) && $tablename != "") {

			$orderby = $this->orderby != "" ? " ORDER BY " . $this->orderby : "";
			$where = $this->where_ != "" ? " WHERE " . $this->where_ : "";
			$limit = $this->limit_ != "" ? $this->limit_ : ""; // " LIMIT 0 , 10";

			$sql = 'SELECT ' . $this->select . ' FROM ' . $tablename . $where . $orderby  . $limit;
			//$sqldo = microtime(true);
			$result = $this->query($sql);
			//$sqlwhide = microtime(true);
			//echo "第一次查询时:".$sql .":" . (($sqlwhide - $sqldo) * 1000) . 'ms</br>';
			$resultarray = $this->getfetch($result);
			if (isset($returnnumber) && $returnnumber === true) {
				//只返带数量

				BaseService::debug()->start("文章总数" . $tablename);
				$sqlcount = "SELECT create_date as total  FROM " . $tablename . $where;
				$resultnum = $this->query($sqlcount);

				BaseService::debug()->end("文章总数" . $tablename);
				return array("total" => $resultnum->num_rows, "list" => $resultarray);
			} else {


				return $resultarray;
			}
		} else {
			return false;
		}
	}



	/**
	 *  更新单条数据
	 * $tablename string 表名 
	 * $val array 数组结构和表结构一致(可以少)
	 * $where  string|array|null update的wehre条件 ,如果为空
	 */
	public  function update($tablename = null, $val = null, $where = null)
	{
	}

	/**
	 *  批量更新
	 * $tablename string 表名 
	 * $val array 二维数组 ,表示多条，每一个数组结构和表结构一致
	 * $where  string 必须存在是字段， 会根据数据里的这个字段更新，数据里必须包含该字段，否则该条数据会忽略
	 * return init|false 如果成功返回成功的条数，否则返回false
	 */
	public  function updateBatch($tablename = null, $val = null, $where = null)
	{
		if (is_string($tablename) && $tablename != "" && is_array($val) && count($val) > 0 && is_string($where)  && $where != "") {

			$field = $this->field_data($tablename);
			if (!is_array($field)  || (is_array($field) && count($field) <= 0)) {
				//非数组，或者数组<0
				return false;
				//
			}
			//var_dump($field);
			$formatdata = $val; //不处理数据 ，测试如果添加不存在的不会报错，但是$where 值不存在会报错
			for ($i = 0; $i < count($formatdata); $i++) {
				$dataitem = $formatdata[$i];
				if (!isset($dataitem[$where])) {
					//将没有设置$where字段的数据剔除
					unset($formatdata[$i]);
				}
			}
			$sql = "UPDATE `" . $tablename . "` SET ";
			$wherearray = [];
			foreach ($field as $item) {
				$casestr = '';
				foreach ($formatdata as $thedata) {
					if (isset($thedata[$item["Field"]])) {
						//该条数据有设置当前字段值
						$casestr .= "WHEN '" . $thedata[$where] . "' THEN '" . addslashes($thedata[$item["Field"]]) . "' ";
					}
					if (!in_array($thedata[$where], $wherearray)) {
						array_push($wherearray, $thedata[$where]);
					}
				}
				if (strlen($casestr) > 0) {
					//该字段有需要更新的数据
					$sql .= " `" . $item["Field"] . "`= CASE `" . $where . "` " . $casestr . " END, ";
				}
			}
			//去掉最后一个END 后面的, 和空格
			$sql = substr($sql, 0, strlen($sql) - 2);
			$sql = $sql . " WHERE `" . $where . "` IN (" . implode(",", $wherearray) . ") ";
			$result = $this->query($sql);
			if ($result) {
				return count($formatdata); //返回更新成功的条数
			} else {
				return $result;
			}
		} else {
			return false;
		}
	}
	/**
	 *  批量插入
	 * $tablename string 表名 
	 * $val array 二维数组 ,表示多条，每一个数组结构和表结构一致
	 * $isid bool 是否保留id 默认 false ，忽略id 为ture 表示保留id
	 * return init|false 如果成功返回成功的条数，否则返回false
	 */
	public  function insertBatch($tablename = null, $val = null, $isid = false)
	{
		if (is_string($tablename) && $tablename != "" && is_array($val) && count($val) > 0) {
			$field = $this->field_data($tablename); //获取表字段
			if (!is_array($field)  || (is_array($field) && count($field) <= 0)) {
				//非数组，或者数组<0
				return false;
				//
			}
			$sql = "";
			$fieldarray = []; //字段数组


			foreach ($val as $dataitem) {
				$theval = [];
				foreach ($field as $fielditem) {
					if (!$isid && $fielditem["Extra"] == "auto_increment") {
						continue;
					}

					if ($fielditem["Extra"] != 'on update CURRENT_TIMESTAMP') {
						if ($fielditem["Type"] != "datetime" && $fielditem["Default"] != "CURRENT_TIMESTAMP") {
							if (!in_array($fielditem["Field"], $fieldarray)) {
								//生成插入前的字段
								array_push($fieldarray, $fielditem["Field"]); //需要插入字段
							}
							if (isset($dataitem[$fielditem["Field"]])) {
								array_push($theval, addslashes($dataitem[$fielditem["Field"]]));
							} else {

								array_push($theval, $fielditem["Default"]);
							}
						}
					}
				}

				$sql .= "('" . implode("','", $theval) . "'), "; //

			}

			$sql = "INSERT INTO " . $tablename . "" . " (" . implode(",", $fieldarray) . ") VALUES " . $sql;

			$sql =	substr($sql, 0, strlen($sql) - 2);
			//echo $sql;
			$result = $this->query($sql);
			
			if ($result > 0) {
				/*
				$ids = [$result];
				for ($i = 1; $i < count($val); $i++) {
					array_push($ids, $result + 1);
				}
				return $ids;
				*/
				return $result; //返回最后一个id
			} else {
				return false;
			}
			//INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date'),  ('Another title', 'Another name', 'Another date')

		} else {
			return false;
		}
	}
	/**
	 * 删除
	 * 1.db->where("id",1)->delete("tabname");//可以配合where ,where_in 等等
	 * 2.db->delete("tabname","id=1");
	 * 3.db->delete("tabname",array("id"=>1));db->delete("tabname",array("id >"=>1))
	 */
	public  function delete($tablename = null, $where = null)
	{
		if (is_string($tablename) && $tablename != "") {
			$wherestr = '';
			$sql = '';
			if (isset($where)) {

				if (is_string($where) && $where != "") {
					$wherestr = $where;
				} else if (is_array($where) && count($where) > 0) {

					foreach ($where as $key => $item) {
						$where_c = '';
						if (strpos($key, "!") !== false || strpos($key, ">") !== false || strpos($key, "<") !== false || strpos($key, "=") !== false) {
							if (is_numeric($item)) {
								$where_c = $key  . $item;
							} else if (is_string($item)) {
								$where_c = $key . "'" . $item . "'";
							}
						} else {
							if (is_numeric($item)) {
								$where_c = $key  . "="  . $item;
							} else if (is_string($item)) {
								$where_c = $key  . "=" . "'" . $item . "'";
							}
						}

						if (strlen($wherestr) > 0) {
							$wherestr .= " AND " . $wherestr;
						} else {
							$wherestr = $where_c;
						}
					} //end foreach


				} else {
					return false;
				}

				$wherestr = $wherestr != "" ? " WHERE " . $wherestr : "";
			} else {
				//使用where
				$wherestr = $this->where_ != "" ? " WHERE " . $this->where_ : "";
			}
			$sql = "DELETE FROM " . $tablename . $wherestr;
			$result = $this->query($sql);
			return $result;
		} else {
			return false;
		}
	}
}
