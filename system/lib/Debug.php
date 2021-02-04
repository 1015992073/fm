<?php


/**

 *
 * @package   调试，记录运行时间和内存

 * @filesource
 */

namespace system\lib;



/**
 * Handle system messages and localization.
 *
 * Locale-based, built on top of PHP internationalization.
 *
 * @package system\lib
 */
class Debug
{

	protected $tiemdata = []; //时间
	protected $memorydata = []; //内存
	protected $result = []; //最终结果
	protected $isdebug = true; //

	//--------------------------------------------------------------------

	public function __construct()
	{
	}

	public  function start(string $tag = 'start', array $arguments = [])
	{
		if ($this->isdebug) {
			if (!isset($this->tiemdata[$tag])) {
				$this->tiemdata[$tag]["start"] = microtime(true);
			}
			if (!isset($this->memorydata[$tag])) {
				$this->memorydata[$tag]["start"] = memory_get_usage();
			}
		}
	}
	public  function end(string $tag = 'end', array $arguments = [])
	{
		if ($this->isdebug) {
			if (isset($this->tiemdata[$tag])) {
				$this->tiemdata[$tag]["end"] = microtime(true);
			}
			if (isset($this->memorydata[$tag])) {
				$this->memorydata[$tag]["end"] = memory_get_usage();
			}
			$this->result[$tag] = $tag . '运行用时:' . round((($this->tiemdata[$tag]["end"] - $this->tiemdata[$tag]["start"]) * 1000), 2) . 'ms ' . ' 内存使用:' . round(($this->memorydata[$tag]["end"] - $this->memorydata[$tag]["start"]) / 1024 / 1024, 4) . 'MB';
			//echo '</br>' . $tag . '运行用时:' . (($this->tiemdata[$tag]["end"] - $this->tiemdata[$tag]["start"]) * 1000) . 'ms ' . ' 内存使用:' . round(($this->memorydata[$tag]["end"] - $this->memorydata[$tag]["start"]) / 1024 / 1024, 4) . 'MB' . "<br>";
		}
	}
	//打印结果
	public  function print()
	{

		if ($this->isdebug) {
			var_dump($this->result);
		}
	}


	//生成多少条数据  $average :每次多少条提交一次
	/**
	 * $format 数据格式要求 如：
	 * 1.array("name"=>"chinese,10,100");字段name 是100-100中文字符，如果是array("name"=>"chinese,10")表示10个中文字符
	 * 2.array("name"=>"chart,10,100");字段name 是100-100英文字符，如果是array("name"=>"chart,10")表示10个英文字符
	 * 3.array("name"=>array("dd","1"));字段name 是从数组随机取1个
	 * 4.array("name"=>"num,1,10";字段name 是从1.2~10 数字
	 * 4.array("name"=>"float,1,10";字段name 是从1.0~10.0 
	 * 4.array("name"=>"date,20200101,20201231";字段name 是从20200101~20201231 日期
	 * 
	 * php操作mysql迅速插入上百万数据  https://blog.csdn.net/everdayPHP/article/details/53996057
	 * 
	 * 这个会生 html+body ，所以，应该单独使用
	 */


	public  function createTestData($table = null, $format = null, $num = 1000, $average = 200)
	{
		if (is_string($table) && $table != "" && isset($format) && is_array($format) && count($format) > 0) {
			ini_set('max_execution_time', '0'); //mysql执行时间 
			//$sql    =  BaseService::database();

			$config = BaseService::config();
			$mysql = $config->mysql;
			$conn = mysqli_connect($mysql["hostname"], $mysql["username"], $mysql["password"], $mysql["database"], $mysql["port"]);

			$average = $num <= 200 ? $num : 200;
			// 检测连接
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			} else {
				//$this->conn = $conn;
				mysqli_query($conn, "set names 'UTF8'");
				mysqli_autocommit($conn, FALSE); //这一步很重要  取消mysql的自动提交

				$begin = microtime(true);
				$count = 0;

				$filds = '';
				foreach ($format as $key => $fild) {
					if (strlen($filds) > 0) {
						$filds .= "," . $key;
					} else {
						$filds = $key;
					}
				}
				$filds = '(' . $filds . ')'; //组装需要插入的字段
				$sql = "";
				//防止执行超时
				set_time_limit(0);
				//清空并关闭输出缓存
				ob_end_clean();
				//需要循环的数据
				echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><title>测试数据生成器</title><script >function show(id="status",message=""){document.getElementById(id).innerHTML = message;}</script ></head><body><div id="status"></div><div id="write"></div><div id="message"></div></body></html>';
				flush(); //这个运行一次输出一次
				for ($i = 1; $i <= $num; $i++) {

					$item = '';
					foreach ($format as $key => $fild) {
						$val = $this->getRanval($key, $fild);
						if (strlen($item) > 0) {
							if (is_numeric($val)) {
								$item .= "," . $val;
							} else {
								$item .= "," . "'" . $val . "'";
							}
						} else {
							if (is_numeric($val)) {
								$item = $val;
							} else {
								$item = "'" . $val . "'";
							}
						}
					}
					if (strlen($sql) > 0) {
						$sql .= ", " . "(" . $item . ")";
					} else {
						$sql =  "(" . $item . ")";
					}


					echo '<script >show("status","总数:' . $num . '；当前: ' . $i . ' ；") </script>';
					flush(); //输出


					if ($i % $average == '0' || $i == $num) {
						$sql = "INSERT INTO " . $table . " " . $filds . " VALUES " . $sql;
						mysqli_query($conn, $sql);
						//echo $sql . "</br>";
						mysqli_commit($conn); // 提交事务 
						$sql = "";
						$count = $i;
						//echo '<script language="JavaScript"> document.getElementById("write").innerHTML = "已经写入' . $i . '条"</script>';
						echo '<script >show("write","已经写入' . $i . '条,完成：' . (round($i / $num, 4) * 100) . '%") </script>';
						flush(); //
					}
				}

				mysqli_close($conn);
				$end = microtime(true);
				echo '<script >show("message","总用时' . round((($end - $begin) * 1000), 2) . 'ms，数据:' . $count . '条") </script>';
				flush();
			}
		}
	}
	//批量插入文章 $startid,起始
	public  function createTestPosts($num = 1000, $start = null)
	{
		if (is_numeric($num) && $num > 0) {
			ini_set('max_execution_time', '0'); //mysql执行时间 
			//$db    =  BaseService::database();
			//INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date'),  ('Another title', 'Another name', 'Another date')
			$config = BaseService::config();
			$mysql = $config->mysql;
			$conn = mysqli_connect($mysql["hostname"], $mysql["username"], $mysql["password"], $mysql["database"], $mysql["port"]);

			$average = $num <= 200 ? $num : 200;
			//$startid = (isset($startid) && is_numeric($startid) && $startid >= 1) ? $startid : 1;


			if (isset($start) && is_numeric($start) && $start >= 1) {
				$startid = $start;
			} else {
				$lastpost = get_table_list(['table' => 'posts', "pagesize" => 1, "orderby" => 'post_id DESC', "return" => "list"]); //获取最后一条
				if (isset($lastpost) && is_array($lastpost) && count($lastpost) == 1) {
					$startid = $lastpost[0]["post_id"];
				} else {
					$startid = 0;
				}
			}



			// 检测连接
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			} else {
				//$this->conn = $conn;
				mysqli_query($conn, "set names 'UTF8'");
				mysqli_autocommit($conn, FALSE); //这一步很重要  取消mysql的自动提交

				$begin = microtime(true);
				$count = 0;

				//$catlist =	$db->get("category", 1, 20000);
				$catlist =	get_table_list(['table' => 'category', "return" => "list"]);
				$filds = '(`post_id`, `post_author`, `post_title`, `post_slug`, `post_content`, `post_parent`,`post_url`,`sort`, `publish_date`,   `create_date`, `update_date`)'; //组装需要插入的字段
				$sql = "";
				//防止执行超时
				set_time_limit(0);
				//清空并关闭输出缓存
				ob_end_clean();
				//需要循环的数据
				echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><title>测试数据生成器</title><script >function show(id="status",message=""){document.getElementById(id).innerHTML = message;}</script ></head><body><div id="status"></div><div id="write"></div><div id="message"></div></body></html>';
				flush(); //这个运行一次输出一次
				for ($i = 1; $i <= $num; $i++) {


					$catiem = $catlist[array_rand($catlist)];
					$theid=$i + $startid;
					$postitem = "(" . $theid . ", " . mt_rand(1, 4) . ", '" . $this->createWords(mt_rand(7, 16)) . "','" . $this->createString(mt_rand(7, 14)) . "','" . $this->createWords(mt_rand(40, 90), mt_rand(1, 3)) . "','" . $catiem["term_id"] . "','" . $catiem["term_url"] . $theid . ".html" . "','" . mt_rand(0, 50) . "','" . $this->randomdate("20210101", "20210331") . "','" . $this->randomdate("20210101", "20210115") . "','" . $this->randomdate("20210115", "20211231") . "')";
					if (strlen($sql) > 0) {
						$sql .= ", " . $postitem;
					} else {
						$sql .= $postitem;
					}


					echo '<script >show("status","总数:' . $num . '；开始id：'.($startid+1).'；当前: ' . $i . ' ；") </script>';
					flush(); //输出


					if ($i % $average == '0' || $i == $num) {
						$sql = "INSERT INTO posts " . $filds . " VALUES " . $sql;
						mysqli_query($conn, $sql);
						//echo $sql . "</br>-------------------------------------";
						mysqli_commit($conn); // 提交事务 

						$sql = "";
						$count = $i;
						//echo '<script language="JavaScript"> document.getElementById("write").innerHTML = "已经写入' . $i . '条"</script>';
						echo '<script >show("write","已经写入' . $i . '条;完成：' . (round($i / $num, 4) * 100) . '%") </script>';

						flush(); //
					}
				}

				mysqli_close($conn);
				$end = microtime(true);
				echo '<script >show("message","总用时' . round((($end - $begin) * 1000), 2) . 'ms，数据:' . $count . '条") </script>';
				flush();
			}
		}
	}

	//根据规则设定字段随机值
	public  function getRanval($key = null, $fild = null)
	{
		if (is_array($fild)) {
			$val = $fild[mt_rand(0, count($fild) - 1)]; //随机数组值
		} else if (is_string($fild)) {
			$valarray = explode(",", $fild);
			if (is_array($valarray) && count($valarray) > 0) {
				switch ($valarray[0]) {

					case "chinese":
						BaseService::debug()->start("生成中文" . $key);
						if (count($valarray) == 2) {
							$val = $this->createWords((int)$valarray[1]);
						} else if (count($valarray) == 3) {
							$val = $this->createWords(mt_rand((int)$valarray[1], (int)$valarray[2]));
						} else if (count($valarray) == 4) {
							$val = $this->createWords(mt_rand((int)$valarray[1], (int)$valarray[2]), (int)$valarray[3]);
						} else {
							$val = $this->createWords();
						}
						BaseService::debug()->end("生成中文" . $key);
						break;
					case "chart":
						BaseService::debug()->start("生成英文" . $key);
						if (count($valarray) == 2) {
							$val = $this->createString((int)$valarray[1]);
						} else if (count($valarray) == 3) {
							$val = $this->createString(mt_rand((int)$valarray[1], (int)$valarray[2]));
						} else {
							$val = $this->createString();
						}
						BaseService::debug()->end("生成英文" . $key);
						break;
					case "num":
						BaseService::debug()->start("生成数字");
						if (count($valarray) == 2) {
							$val = mt_rand(0, (int)$valarray[0]);
						} else if (count($valarray) == 3) {
							$val = mt_rand((int)$valarray[1], (int)$valarray[2]);
						} else {
							$val = mt_rand(0, 100);
						}
						BaseService::debug()->end("生成数字");
						break;
					case "float":
						if (count($valarray) == 2) {
							$val = 0 + mt_rand() / mt_getrandmax() * ($valarray[1] - 0);
						} else if (count($valarray) == 3) {
							$val = $valarray[1] + mt_rand() / mt_getrandmax() * ($valarray[2] - $valarray[1]);
						} else {
							$val = 0 + mt_rand() / mt_getrandmax() * (100 - 0);
						}
						break;
					case "date":
						BaseService::debug()->start("生成日期");
						if (count($valarray) == 2) {
							$val = $this->randomdate($valarray[1]);
						} else if (count($valarray) == 3) {
							$val = $this->randomdate($valarray[1], $valarray[2]);
						} else {
							$val = $this->randomdate();
						}
						BaseService::debug()->end("生成日期");
						break;

					default:
						$val = "default";
						break;
				}
			} else {
				$val = 'err';
			}
		}
		return $val;
	}

	//生成多少个汉字 length 汉字数，不包括标点  line表示段落
	public  function createWords($length = 100, $line = 1)
	{
		$seperate = array("，", "。", "！",  "；");
		$chinese = "一乙二十丁厂七卜人入八九几儿了力乃刀又三于干亏士工土才寸下大丈与万上小口巾山千乞川亿个勺久凡及夕丸么广亡门义之尸弓己已子卫也女飞刃习叉马乡丰王井开夫天无元专云扎艺木五支厅不太犬区历尤友匹车巨牙屯比互切瓦止少日中冈贝内水见午牛手毛气升长仁什片仆化仇币仍仅斤爪反介父从今凶分乏公仓月氏勿欠风丹匀乌凤勾文六方火为斗忆订计户认心尺引丑巴孔队办以允予劝双书幻玉刊示末未击打巧正扑扒功扔去甘世古节本术可丙左厉右石布龙平灭轧东卡北占业旧帅归且旦目叶甲申叮电号田由史只央兄叼叫另叨叹四生失禾丘付仗代仙们仪白仔他斥瓜乎丛令用甩印乐句匆册犯外处冬鸟务包饥主市立闪兰半汁汇头汉宁穴它讨写让礼训必议讯记永司尼民出辽奶奴加召皮边发孕圣对台矛纠母幼丝式刑动扛寺吉扣考托老执巩圾扩扫地扬场耳共芒亚芝朽朴机权过臣再协西压厌在有百存而页匠夸夺灰达列死成夹轨邪划迈毕至此贞师尘尖劣光当早吐吓虫曲团同吊吃因吸吗屿帆岁回岂刚则肉网年朱先丢舌竹迁乔伟传乒乓休伍伏优伐延件任伤价份华仰仿伙伪自血向似后行舟全会杀合兆企众爷伞创肌朵杂危旬旨负各名多争色壮冲冰庄庆亦刘齐交次衣产决充妄闭问闯羊并关米灯州汗污江池汤忙兴宇守宅字安讲军许论农讽设访寻那迅尽导异孙阵阳收阶阴防奸如妇好她妈戏羽观欢买红纤级约纪驰巡寿弄麦形进戒吞远违运扶抚坛技坏扰拒找批扯址走抄坝贡攻赤折抓扮抢孝均抛投坟抗坑坊抖护壳志扭块声把报却劫芽花芹芬苍芳严芦劳克苏杆杠杜材村杏极李杨求更束豆两丽医辰励否还歼来连步坚旱盯呈时吴助县里呆园旷围呀吨足邮男困吵串员听吩吹呜吧吼别岗帐财针钉告我乱利秃秀私每兵估体何但伸作伯伶佣低你住位伴身皂佛近彻役返余希坐谷妥含邻岔肝肚肠龟免狂犹角删条卵岛迎饭饮系言冻状亩况床库疗应冷这序辛弃冶忘闲间闷判灶灿弟汪沙汽沃泛沟没沈沉怀忧快完宋宏牢究穷灾良证启评补初社识诉诊词译君灵即层尿尾迟局改张忌际陆阿陈阻附妙妖妨努忍劲鸡驱纯纱纳纲驳纵纷纸纹纺驴纽奉玩环武青责现表规抹拢拔拣担坦押抽拐拖拍者顶拆拥抵拘势抱垃拉拦拌幸招坡披拨择抬其取苦若茂苹苗英范直茄茎茅林枝杯柜析板松枪构杰述枕丧或画卧事刺枣雨卖矿码厕奔奇奋态欧垄妻轰顷转斩轮软到非叔肯齿些虎虏肾贤尚旺具果味昆国昌畅明易昂典固忠咐呼鸣咏呢岸岩帖罗帜岭凯败贩购图钓制知垂牧物乖刮秆和季委佳侍供使例版侄侦侧凭侨佩货依的迫质欣征往爬彼径所舍金命斧爸采受乳贪念贫肤肺肢肿胀朋股肥服胁周昏鱼兔狐忽狗备饰饱饲变京享店夜庙府底剂郊废净盲放刻育闸闹郑券卷单炒炊炕炎炉沫浅法泄河沾泪油泊沿泡注泻泳泥沸波泼泽治怖性怕怜怪学宝宗定宜审宙官空帘实试郎诗肩房诚衬衫视话诞询该详建肃录隶居届刷屈弦承孟孤陕降限妹姑姐姓始驾参艰线练组细驶织终驻驼绍经贯奏春帮珍玻毒型挂封持项垮挎城挠政赴赵挡挺括拴拾挑指垫挣挤拼挖按挥挪某甚革荐巷带草茧茶荒茫荡荣故胡南药标枯柄栋相查柏柳柱柿栏树要咸威歪研砖厘厚砌砍面耐耍牵残殃轻鸦皆背战点临览竖省削尝是盼眨哄显哑冒映星昨畏趴胃贵界虹虾蚁思蚂虽品咽骂哗咱响哈咬咳哪炭峡罚贱贴骨钞钟钢钥钩卸缸拜看矩怎牲选适秒香种秋科重复竿段便俩贷顺修保促侮俭俗俘信皇泉鬼侵追俊盾待律很须叙剑逃食盆胆胜胞胖脉勉狭狮独狡狱狠贸怨急饶蚀饺饼弯将奖哀亭亮度迹庭疮疯疫疤姿亲音帝施闻阀阁差养美姜叛送类迷前首逆总炼炸炮烂剃洁洪洒浇浊洞测洗活派洽染济洋洲浑浓津恒恢恰恼恨举觉宣室宫宪突穿窃客冠语扁袄祖神祝误诱说诵垦退既屋昼费陡眉孩除险院娃姥姨姻娇怒架贺盈勇怠柔垒绑绒结绕骄绘给络骆绝绞统耕耗艳泰珠班素蚕顽盏匪捞栽捕振载赶起盐捎捏埋捉捆捐损都哲逝捡换挽热恐壶挨耻耽恭莲莫荷获晋恶真框桂档桐株桥桃格校核样根索哥速逗栗配翅辱唇夏础破原套逐烈殊顾轿较顿毙致柴桌虑监紧党晒眠晓鸭晃晌晕蚊哨哭恩唤啊唉罢峰圆贼贿钱钳钻铁铃铅缺氧特牺造乘敌秤租积秧秩称秘透笔笑笋债借值倚倾倒倘俱倡候俯倍倦健臭射躬息徒徐舰舱般航途拿爹爱颂翁脆脂胸胳脏胶脑狸狼逢留皱饿恋桨浆衰高席准座脊症病疾疼疲效离唐资凉站剖竞部旁旅畜阅羞瓶拳粉料益兼烤烘烦烧烛烟递涛浙涝酒涉消浩海涂浴浮流润浪浸涨烫涌悟悄悔悦害宽家宵宴宾窄容宰案请朗诸读扇袜袖袍被祥课谁调冤谅谈谊剥恳展剧屑弱陵陶陷陪娱娘通能难预桑绢绣验继球理捧堵描域掩捷排掉堆推掀授教掏掠培接控探据掘职基著勒黄萌萝菌菜萄菊萍菠营械梦梢梅检梳梯桶救副票戚爽聋袭盛雪辅辆虚雀堂常匙晨睁眯眼悬野啦晚啄距跃略蛇累唱患唯崖崭崇圈铜铲银甜梨犁移笨笼笛符第敏做袋悠偿偶偷您售停偏假得衔盘船斜盒鸽悉欲彩领脚脖脸脱象够猜猪猎猫猛馅馆凑减毫麻痒痕廊康庸鹿盗章竟商族旋望率着盖粘粗粒断剪兽清添淋淹渠渐混渔淘液淡深婆梁渗情惜惭悼惧惕惊惨惯寇寄宿窑密谋谎祸谜逮敢屠弹随蛋隆隐婚婶颈绩绪续骑绳维绵绸绿琴斑替款堪搭塔越趁趋超提堤博揭喜插揪搜煮援裁搁搂搅握揉斯期欺联散惹葬葛董葡敬葱落朝辜葵棒棋植森椅椒棵棍棉棚棕惠惑逼厨厦硬确雁殖裂雄暂雅辈悲紫辉敞赏掌晴暑最量喷晶喇遇喊景践跌跑遗蛙蛛蜓喝喂喘喉幅帽赌赔黑铸铺链销锁锄锅锈锋锐短智毯鹅剩稍程稀税筐等筑策筛筒答筋筝傲傅牌堡集焦傍储奥街惩御循艇舒番释禽腊脾腔鲁猾猴然馋装蛮就痛童阔善羡普粪尊道曾焰港湖渣湿温渴滑湾渡游滋溉愤慌惰愧愉慨割寒富窜窝窗遍裕裤裙谢谣谦属屡强粥疏隔隙絮嫂登缎缓编骗缘瑞魂肆摄摸填搏塌鼓摆携搬摇搞塘摊蒜勤鹊蓝墓幕蓬蓄蒙蒸献禁楚想槐榆楼概赖酬感碍碑碎碰碗碌雷零雾雹输督龄鉴睛睡睬鄙愚暖盟歇暗照跨跳跪路跟遣蛾蜂嗓置罪罩错锡锣锤锦键锯矮辞稠愁筹签简毁舅鼠催傻像躲微愈遥腰腥腹腾腿触解酱痰廉新韵意粮数煎塑慈煤煌满漠源滤滥滔溪溜滚滨粱滩慎誉塞谨福群殿辟障嫌嫁叠缝缠静碧璃墙撇嘉摧截誓境摘摔聚蔽慕暮蔑模榴榜榨歌遭酷酿酸磁愿需弊裳颗嗽蜻蜡蝇蜘赚锹锻舞稳算箩管僚鼻魄貌膜膊膀鲜疑馒裹敲豪膏遮腐瘦辣竭端旗精歉熄熔漆漂漫滴演漏慢寨赛察蜜谱嫩翠熊凳骡缩慧撕撒趣趟撑播撞撤增聪鞋蕉蔬横槽樱橡飘醋醉震霉瞒题暴瞎影踢踏踩踪蝶蝴嘱墨镇靠稻黎稿稼箱箭篇僵躺僻德艘膝膛熟摩颜毅糊遵潜潮懂额慰劈操燕薯薪薄颠橘整融醒餐嘴蹄器赠默镜赞篮邀衡膨雕磨凝辨辩糖糕燃澡激懒壁避缴戴擦鞠藏霜霞瞧蹈螺穗繁辫赢糟糠燥臂翼骤鞭覆蹦镰翻鹰警攀蹲颤瓣爆疆壤耀躁嚼嚷籍魔灌蠢霸露囊罐";
		$strLen = mb_strlen($chinese, 'UTF8') - 1;
		$line = (is_numeric($line) && $line >= 1) ? $line : 1;
		$seperateindex = rand(5, 12); //下次标点符号出现的地方
		$strings = '';
		for ($j = 0; $j < $line; $j++) {
			for ($i = 0; $i < $length; $i++) {
				//$strings .= iconv('GB2312', 'utf-8', chr(mt_rand(0xB0, 0xD0)) . chr(mt_rand(0xA1, 0xF0)));
				//$strings .= $chinese[mt_rand(0, strlen($chinese) - 1)];
				$strings .= mb_substr($chinese, floor(mt_rand(0, $strLen)), 1, 'UTF8');
				if ($i == $seperateindex && $seperateindex < ($length - 3)) {
					$strings .= $seperate[mt_rand(0, 3)];
					$seperateindex = $i + mt_rand(5, 14);
				}
			}
			if ($line > 1) {
				$strings .= '\r\n';
			}
		}
		return $strings;
	}

	//生成英文句子，$length 单词数，不包括标点
	public  function createString($length = 100, $line = 1)
	{
		// 密码字符集，可任意添加你需要的字符 
		$chars = 'abcdefghijklmnopqrstuvwxyz';
		$seperate = array(",", ".", "!",  ";");
		$seperateindex = mt_rand(5, 10); //下次标点符号出现的地方
		$str = '';
		for ($j = 0; $j < $line; $j++) {
			for ($i = 0; $i < $length; $i++) {
				// 这里提供两种字符获取方式 
				// 第一种是使用 substr 截取$chars中的任意一位字符； 
				// 第二种是取字符数组 $chars 的任意元素 
				// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1); 
				$strlen = mt_rand(2, 8); //单词长度
				$thestr = '';
				for ($j = 0; $j < $strlen; $j++) {
					$thestr .= $chars[mt_rand(0, strlen($chars) - 1)];
				}
				if ($i == $seperateindex && $seperateindex < ($length - 1)) {
					$thestr .= $seperate[mt_rand(0, 3)] . " "; //加标点
					$seperateindex = $i + mt_rand(5, 14);
				} else {
					$thestr .= " "; //加空格
				}
				$str .= $thestr; //加空格

			}
			if ($line > 1) {
				$str .= '\r\n';
			}
		}
		return $str;
	}
	//生成随机时间
	public  function randomdate($start = "20210101", $end = "202101231")
	{
		// 密码字符集，可任意添加你需要的字符 
		$start = strtotime($start) ? strtotime($start) : time();
		$end = strtotime($end) ? strtotime($end) : strtotime("+12 month");
		return date('Y-m-d H:i:s', mt_rand($start, $end));
	}

	/**
	 * 生成分类 ,生成前清空表 ,因为id 如果有不不会新增
	 * $level=["10", "5,8", "4,7"];表示分类第一层生成10个分类，第二层随机生成5-8个分类，第三层随机生成4-7个分类
	 * BaseService::debug()->createCatTestData() ;
	 */
	public  function createCatTestData($level = ["10", "5,8", "5,8"])
	{

		$cach = []; //缓存
		$id = 0; //id
		$lastpost = get_table_list(['table' => 'category', "pagesize" => 1, "orderby" => 'term_id DESC', "return" => "list"]); //获取最后一条
		if (isset($lastpost) && is_array($lastpost) && count($lastpost) == 1) {
			$id = $lastpost[0]["term_id"];
		} 

		if (is_array($level) && count($level) > 0) {

			$arg = explode(",", $level[0]);
			if (count($arg) == 1) {
				$num = $arg[0];
			} else if (count($arg) == 2) {
				$num = mt_rand($arg[0], $arg[1]);
			} else {
				$num = 1;
			}

			for ($i = 1; $i <= $num; $i++) {
				$id++;
				$terms_slug = trim($this->createString(1));
				//echo '第1层:' . $terms_slug . '<br>';
				$cach["1"][] = array("term_id" => $id, "term_parents" => 0, "terms_slug" => $terms_slug, "term_url" => $terms_slug . "/", "sort" => mt_rand(0, 50), "terms_description" => $this->createWords(mt_rand(12, 30)), "terms_name" => $this->createWords(mt_rand(2, 6))); //生成第一层
			}

			$levelarray = $cach['1']; //每层数量
			$levelnum = 2; //从第二层开始$level[1];
			while (isset($level[$levelnum - 1]) && $level[$levelnum - 1] != "" && $level[$levelnum - 1] != " ") {
				$arg = explode(",", $level[$levelnum - 1]); //第$levelarray层数据格式

				foreach ($levelarray as $item) {
					//更具规则。每个分类随机生成第$levelarray层的子类
					if (count($arg) == 1) {
						$num = $arg[0];
					} else if (count($arg) == 2) {
						$num = mt_rand($arg[0], $arg[1]);
					} else {
						$num = 1;
					}
					//echo '第'.$levelnum.'层 ：'.$item["terms_slug"].'分类下随机子类数.'.$num.'个<br>';
					for ($i = 1; $i <= $num; $i++) {
						$id++;
						$terms_slug = trim($this->createString(1));
						//$terms_slug = $item["terms_slug"] .  $this->createString(1);
						//echo $item["terms_slug"].'子类'.$terms_slug.'个<br>';
						$url = $item["term_url"] . $terms_slug . "/";

						$cach[$levelnum][] = array("term_id" => $id, "term_parents" => $item["term_id"], "terms_slug" => $terms_slug, "term_url" => $url, "sort" => mt_rand(0, 100), "terms_description" => $this->createWords(mt_rand(12, 30)), "terms_name" => $this->createWords(mt_rand(2, 7))); //生成第$levelarray层;
					}
				}
				$levelarray = $cach[$levelnum]; //设置第$levelarray+1层所有数据
				$levelnum++; //准备下次循环
			}
		}
		$data = [];
		foreach ($cach as $item) {
			$data = array_merge($data, $item);
		}
		//var_dump($data);
		$db  =  BaseService::database();
		//$data1=array(array("term_id" => 30, "term_parents" =>0, "terms_slug" =>  trim($this->createString(1))."/", "sort" => mt_rand(0, 100), "terms_description" => $this->createWords(mt_rand(12, 30)), "terms_name" => $this->createWords(mt_rand(2, 7))));
		$r = $db->insertBatch("category", $data, true); //保留数据里的id
		return $data;
	}


	// 将一个数随机切割多少份
	public function rancutnum($tol = 10, $num = 2)
	{

		if (is_numeric($tol) && is_numeric($num) && $tol > 0 && $num > 0) {
			if ($tol < $num) {
				$num = $tol;
			}
			$c_tol = $tol; //可变总数
			$has_tol = 0; //已经取总数

			for ($i = 1; $i <= $num; $i++) {
				$c_tol = $c_tol - ($num - $i); //每一次能够选择数，总数减去剩余层数
				if ($i == $num) {
					//最后一个，就是剩余的
					$rannum = $c_tol;
				} else {
					$rannum = mt_rand(1, $c_tol); //不是最后一个随机获取数
				}

				$has_tol = $has_tol + $rannum; //已经获取的总数

				$data[] = $rannum; //储存随机获取的值
				$c_tol = $tol - $has_tol; //设置剩余总数

			}



			return $data;
		} else {
			return [];
		}
	}
}
