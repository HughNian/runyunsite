<?php
defined('APP_PATH') or die('No direct access allowed.');
/**
 * 此类属于系统工具类，用于常用功能处理，如获取IP,字符串，时间处理等
 * 
 * 
 */
class UtilityHelper
{
    /**
     * 获取ip地址
     * @return string ip - 客户端IP地址
     */
    public static function getip(){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            if($_SERVER['HTTP_X_FORWARDED_FOR'])
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
            if($_SERVER['HTTP_CLIENT_IP'])
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(isset($_SERVER['REMOTE_ADDR'])){
            if($_SERVER['REMOTE_ADDR'])
            return $_SERVER['REMOTE_ADDR'];
        }
        elseif(getenv('HTTP_XFORWARDED_FOR')){
            return getenv('HTTP_XFORWARDED_FOR');
        }
        elseif(getenv('HTTP_CLIENT_IP')){
            return getenv('HTTP_CLIENT_IP');
        }
        elseif(getenv('REMOTE_ADDR')){
            return getenv('REMOTE_ADDR');
        }                
        else{
            return 'unknown';
        }
                            
    }

    /**
     * 生成指定时间格式的时间戳，需要使用'$timestamp===false'判断，结果不为false，则为时间戳
     * @param string $strDateTime - 时间的字符串格式，支持三种格式('20110818','20110818095410','2011/08/18 09:54:10')
     * @param boolean $zerotimestamp - 是否只生成当天零时的时间戳
     * @return int $timeStamp - 给定字符串的时间戳，如果非合法字符串，则返回false。
     */
    public static function toTimeStamp($strDateTime, $zerotimestamp=false){
        $timeStamp = false;
        
        if (preg_match('/^\d{8}$/', $strDateTime)) {
            //时间参数$strDateTime='20110818'的格式处理
            //如果是此类格式，强制$zerotimestamp值为true,只返回当天的零点时间戳
            list($y1, $y2, $month, $day) = str_split($strDateTime, 2);
            $year = $y1 . $y2;
            $zerotimestamp = true;
        } elseif (preg_match('/^\d{14}$/', $strDateTime)) {
            //时间参数$strDateTime='20110818092914'的格式处理
            list($y1, $y2, $month, $day, $hour, $min, $sec) = str_split($strDateTime, 2);
            $year = $y1 . $y2;
        } elseif (preg_match('/^\d{4}[-\/]\d{1,2}[-\/]\d{1,2}[T ](\d{1,2}[:]){2}\d{1,2}$/', $strDateTime)) {
            //时间参数$strDateTime='2011[-/](0)8[-/]18 (0)9:29:14的格式处理
            //[-/]表示日期分隔符可以是'-'或'/'，(0)表示可有可无
            list($year, $month, $day, $hour, $min, $sec) = split('[-T/: ]', $strDateTime);
        } else {
            return $timeStamp;
        }
        
        /*
         * 不处理检查日期的合法性，如果输入的格式为2011-13-21 9:40:20，则得到的结果是
         * 2012-1-21 9:40:20的时间戳。需要使用时将下行中的'\'去掉即可。
         *\/
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        //*/
        
        if (!$zerotimestamp) {
            $timeStamp = mktime($hour, $min, $sec, $month, $day, $year);
        } else {
            $timeStamp = mktime(0, 0 , 0, $month, $day, $year);
        }
        return  $timeStamp;
    }
        
    /**
     * 检查目录是否存在并建立，使用相对路径。
     * @param string $dir - 要建立的目录
     * @param string $basedir - 根目录
     * @return int - 执行结果，0为存在此目录或建立目录成功，-1为建立目录失败
     * 
     */
    public static function checkdir($dir, $basedir='./')
    {
        if (is_dir($dir)) return 0;
        $dirs = explode(DIRECTORY_SEPARATOR, str_replace(array($basedir, '/', '\\'), array('', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $dir));
        for ($i = 0; $i < count($dirs); $i++) {
            $tempdir = implode('/', array_slice($dirs, 0, $i+1));
            $tempdir = $basedir . $tempdir;
            if (!is_dir($tempdir)) {
                @mkdir($tempdir, 0777);
                @chmod($tempdir, 0777);
                if (!is_dir($tempdir) || !is_readable($tempdir) || !is_writable($tempdir)) return -1;
            }
        }
        return 0;
    }

    /**
     * 获取文件扩展名
     * @param String $name     文件名词
     * @author 
     */
    public static function getExtension($name)
    {
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        return  $ext ? $ext : '';
    }
    
    /**
     * hash-md5-core
     * @param mixed $crypt - 要进行md5的值
     * @param string $salt - 干扰值
     * @return string - 进行md5-crypt后的值
     */
    public static function getMd5Core($crypt, $salt='')
    {
        return substr(hash('md5', $crypt . $salt), 8, 16);
    }
    
    /**
     * 解析绝对地址路径，此处的绝对路径指的是文件系统上的绝对路径，如E:\servers\
     * @param string $baseUrl - 基础路径
     * @return string - 绝对路径
     */
    public static function resolveUrl($baseUrl)
    {
        return self::getDocumentRootPath() . $baseUrl;
    }
    
    /**
     * 获得DocumentRoot的绝对路径
     * @return string - 绝对路径
     */
    public static function getDocumentRootPath()
    {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $sRealPath = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR;
        }
        else {
            $sRealPath = realpath( './' ) ;
        }

        $sSelfPath = dirname($_SERVER['PHP_SELF']);

        return substr($sRealPath, 0, strlen($sRealPath) - strlen($sSelfPath));
    }
    
    /**
	 * 生成密码干扰码(大小写+数字,随机6位)(2011-11-2)
	 * @author 刘阳(alexdany@126.com)
	 *
	 * @return string
	 */
	public static function genSalt() {
		$arrChars = str_split('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 1);
		$arrRandomChars = array_rand($arrChars, 6);
		foreach ($arrRandomChars as $key => $val) {
			$arrRandomChars[$key] = $arrChars[$val];
		}
		$str = join('', $arrRandomChars);
		return $str;

	}// end of function genSalt
	
	public static function mkPasswd($pwd, $salt)
	{
	    return md5(md5($pwd) . $salt);
	}
	
	/**
	 * 生成快速注册的用户名
	 */
	public static function genUsername($id)
	{
	    $prefix = 'hjs';
	    $base = 100;
	    $id = $prefix . (($base+$id)*100+mt_rand(0,99));
	    return $id;
	}
	
	/**
	 * 生成随机6位密码
	 */
	public static function genRandPwd($len=6)
	{
	    $arrChars = str_split('0123456789abcdefghijklmnopqrstuvwxyz', 1);
	    $arrRandomChars = array_rand($arrChars, $len);
	    foreach ($arrRandomChars as $key => $val) {
	        $arrRandomChars[$key] = $arrChars[$val];
	    }
	    $str = join('', $arrRandomChars);
	    return $str;
	}
	
	public static function getPluralFormula($locale)
    {
        if ($locale == "pt_BR") {
            // temporary set a locale for brasilian
            $locale = "xbr";
        }
    
        if (strlen($locale) > 3) {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
        }
    
        switch($locale) {
            case 'bo':
            case 'dz':
            case 'id':
            case 'ja':
            case 'jv':
            case 'ka':
            case 'km':
            case 'kn':
            case 'ko':
            case 'ms':
            case 'th':
            case 'tr':
            case 'vi':
            case 'zh':
                return 0;
                break;
    
            case 'af':
            case 'az':
            case 'bn':
            case 'bg':
            case 'ca':
            case 'da':
            case 'de':
            case 'el':
            case 'en':
            case 'eo':
            case 'es':
            case 'et':
            case 'eu':
            case 'fa':
            case 'fi':
            case 'fo':
            case 'fur':
            case 'fy':
            case 'gl':
            case 'gu':
            case 'ha':
            case 'he':
            case 'hu':
            case 'is':
            case 'it':
            case 'ku':
            case 'lb':
            case 'ml':
            case 'mn':
            case 'mr':
            case 'nah':
            case 'nb':
            case 'ne':
            case 'nl':
            case 'nn':
            case 'no':
            case 'om':
            case 'or':
            case 'pa':
            case 'pap':
            case 'ps':
            case 'pt':
            case 'so':
            case 'sq':
            case 'sv':
            case 'sw':
            case 'ta':
            case 'te':
            case 'tk':
            case 'ur':
            case 'zu':
                return '(n == 1) ? 0 : 1';
    
            case 'am':
            case 'bh':
            case 'fil':
            case 'fr':
            case 'gun':
            case 'hi':
            case 'ln':
            case 'mg':
            case 'nso':
            case 'xbr':
            case 'ti':
            case 'wa':
                return '((n == 0) || (n == 1)) ? 0 : 1';
    
            case 'be':
            case 'bs':
            case 'hr':
            case 'ru':
            case 'sr':
            case 'uk':
                return '((n % 10 == 1) && (n % 100 != 11)) ? 0 : (((n % 10 >= 2) && (n % 10 <= 4) && ((n % 100 < 10) || (n % 100 >= 20))) ? 1 : 2)';
    
            case 'cs':
            case 'sk':
                return '(n == 1) ? 0 : (((n >= 2) && (n <= 4)) ? 1 : 2)';
    
            case 'ga':
                return '(n == 1) ? 0 : ((n == 2) ? 1 : 2)';
    
            case 'lt':
                return '((n % 10 == 1) && (n % 100 != 11)) ? 0 : (((n % 10 >= 2) && ((n % 100 < 10) || (n % 100 >= 20))) ? 1 : 2)';
    
            case 'sl':
                return '(n % 100 == 1) ? 0 : ((n % 100 == 2) ? 1 : (((n % 100 == 3) || (n % 100 == 4)) ? 2 : 3))';
    
            case 'mk':
                return '(n % 10 == 1) ? 0 : 1';
    
            case 'mt':
                return '(n == 1) ? 0 : (((n == 0) || ((n % 100 > 1) && (n % 100 < 11))) ? 1 : (((n % 100 > 10) && (n % 100 < 20)) ? 2 : 3))';
    
            case 'lv':
                return '(n == 0) ? 0 : (((n % 10 == 1) && (n % 100 != 11)) ? 1 : 2)';
    
            case 'pl':
                return '(n == 1) ? 0 : (((n % 10 >= 2) && (n % 10 <= 4) && ((n % 100 < 12) || (n % 100 > 14))) ? 1 : 2)';
    
            case 'cy':
                return '(n == 1) ? 0 : ((n == 2) ? 1 : (((n == 8) || (n == 11)) ? 2 : 3))';
    
            case 'ro':
                return '(n == 1) ? 0 : (((n == 0) || ((n % 100 > 0) && (n % 100 < 20))) ? 1 : 2)';
    
            case 'ar':
                return '(n == 0) ? 0 : ((n == 1) ? 1 : ((n == 2) ? 2 : (((n >= 3) && (n <= 10)) ? 3 : (((n >= 11) && (n <= 99)) ? 4 : 5))))';
    
            default:
                return 0;
        }
    }
    
    /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    public static function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    	if(function_exists("mb_substr"))
    		$slice = mb_substr($str, $start, $length, $charset);
    	elseif(function_exists('iconv_substr')) {
    		$slice = iconv_substr($str,$start,$length,$charset);
    		if(false === $slice) {
    			$slice = '';
    		}
    	}else{
    		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    		preg_match_all($re[$charset], $str, $match);
    		$slice = join("",array_slice($match[0], $start, $length));
    	}
    	return $suffix ? $slice.'...' : $slice;
    }
    
    /**
     * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
     * @param string $len 长度
     * @param string $type 字串类型
     * 0 字母 1 数字 其它 混合
     * @param string $addChars 额外字符
     * @return string
     */
    public static function rand_string($len=6,$type='',$addChars='') {
    	$str ='';
    	switch($type) {
    		case 0:
    			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
    			break;
    		case 1:
    			$chars= str_repeat('0123456789',3);
    			break;
    		case 2:
    			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
    			break;
    		case 3:
    			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
    			break;
    		case 4:
    			$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
    			break;
    		default :
    			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
    			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
    			break;
    	}
    	if($len>10 ) {//位数过长重复字符串一定次数
    		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    	}
    	if($type!=4) {
    		$chars   =   str_shuffle($chars);
    		$str     =   substr($chars,0,$len);
    	}else{
    		// 中文随机字
    		for($i=0;$i<$len;$i++){
    			$str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
    		}
    	}
    	return $str;
    }
    
    /**
     * 检查字符串是否是UTF8编码
     * @param string $string 字符串
     * @return Boolean
     */
    function is_utf8($string) {
    	return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);
    }
    
    /**
     * 代码加亮
     * @param String  $str 要高亮显示的字符串 或者 文件名
     * @param Boolean $show 是否输出
     * @return String
     */
    public static function highlight_code($str,$show=false) {
    	if(file_exists($str)) {
    		$str    =   file_get_contents($str);
    	}
    	$str  =  stripslashes(trim($str));
    	// The highlight string function encodes and highlights
    	// brackets so we need them to start raw
    	$str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);
    
    	// Replace any existing PHP tags to temporary markers so they don't accidentally
    	// break the string out of PHP, and thus, thwart the highlighting.
    
    	$str = str_replace(array('&lt;?php', '?&gt;',  '\\'), array('phptagopen', 'phptagclose', 'backslashtmp'), $str);
    
    	// The highlight_string function requires that the text be surrounded
    	// by PHP tags.  Since we don't know if A) the submitted text has PHP tags,
    	// or B) whether the PHP tags enclose the entire string, we will add our
    	// own PHP tags around the string along with some markers to make replacement easier later
    
    	$str = '<?php //tempstart'."\n".$str.'//tempend ?>'; // <?
    
    	// All the magic happens here, baby!
    	$str = highlight_string($str, TRUE);
    
    	// Prior to PHP 5, the highlight function used icky font tags
    	// so we'll replace them with span tags.
    	if (abs(phpversion()) < 5) {
    		$str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
    		$str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
    	}
    
    	// Remove our artificially added PHP
    	$str = preg_replace('#\<code\>.+?//tempstart\<br />\</span\>#is', "<code>\n", $str);
    	$str = preg_replace('#\<code\>.+?//tempstart\<br />#is', "<code>\n", $str);
    	$str = preg_replace('#//tempend.+#is', "</span>\n</code>", $str);
    
    	// Replace our markers back to PHP tags.
    	$str = str_replace(array('phptagopen', 'phptagclose', 'backslashtmp'), array('&lt;?php', '?&gt;', '\\'), $str); //<?
    	$line   =   explode("<br />", rtrim(ltrim($str,'<code>'),'</code>'));
    	$result =   '<div class="code"><ol>';
    	foreach($line as $key=>$val) {
    		$result .=  '<li>'.$val.'</li>';
    	}
    	$result .=  '</ol></div>';
    	$result = str_replace("\n", "", $result);
    	if( $show!== false) {
    		echo($result);
    	}else {
    		return $result;
    	}
    }
    
    //输出安全的html
    public static function h($text, $tags = null) {
    	$text	=	trim($text);
    	//完全过滤注释
    	$text	=	preg_replace('/<!--?.*-->/','',$text);
    	//完全过滤动态代码
    	$text	=	preg_replace('/<\?|\?'.'>/','',$text);
    	//完全过滤js
    	$text	=	preg_replace('/<script?.*\/script>/','',$text);
    
    	$text	=	str_replace('[','&#091;',$text);
    	$text	=	str_replace(']','&#093;',$text);
    	$text	=	str_replace('|','&#124;',$text);
    	//过滤换行符
    	$text	=	preg_replace('/\r?\n/','',$text);
    	//br
    	$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
    	$text	=	preg_replace('/<p(\s\/)?'.'>/i','[br]',$text);
    	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
    	//过滤危险的属性，如：过滤on事件lang js
    	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
    		$text=str_replace($mat[0],$mat[1],$text);
    	}
    	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
    		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
    	}
    	if(empty($tags)) {
    		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
    	}
    	//允许的HTML标签
    	$text	=	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
    	$text = preg_replace('/<\/('.$tags.')>/Ui','[/\1]',$text);
    	//过滤多余html
    	$text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
    	//过滤合法的html标签
    	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
    		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
    	}
    	//转换引号
    	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
    		$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
    	}
    	//过滤错误的单个引号
    	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
    		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
    	}
    	//转换其它所有不合法的 < >
    	$text	=	str_replace('<','&lt;',$text);
    	$text	=	str_replace('>','&gt;',$text);
    	$text	=	str_replace('"','&quot;',$text);
    	//反转换
    	$text	=	str_replace('[','<',$text);
    	$text	=	str_replace(']','>',$text);
    	$text	=	str_replace('|','"',$text);
    	//过滤多余空格
    	$text	=	str_replace('  ',' ',$text);
    	return $text;
    }
    
    // 随机生成一组字符串
    public static function build_count_rand ($number,$length=4,$mode=1) {
    	if($mode==1 && $length<strlen($number) ) {
    		//不足以生成一定数量的不重复数字
    		return false;
    	}
    	$rand   =  array();
    	for($i=0; $i<$number; $i++) {
    		$rand[] = self::rand_string($length,$mode);
    	}
    	$unqiue = array_unique($rand);
    	if(count($unqiue)==count($rand)) {
    		return $rand;
    	}
    	$count   = count($rand)-count($unqiue);
    	for($i=0; $i<$count*3; $i++) {
    		$rand[] = self::rand_string($length,$mode);
    	}
    	$rand = array_slice(array_unique ($rand),0,$number);
    	return $rand;
    }
    
    public static function remove_xss($val) {
    	// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    	// this prevents some character re-spacing such as <java\0script>
    	// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    	$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    
    	// straight replacements, the user should never need these since they're normal characters
    	// this prevents like <IMG SRC=@avascript:alert('XSS')>
    	$search = 'abcdefghijklmnopqrstuvwxyz';
    	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$search .= '1234567890!@#$%^&*()';
    	$search .= '~`";:?+/={}[]-_|\'\\';
    	for ($i = 0; $i < strlen($search); $i++) {
    		// ;? matches the ;, which is optional
    		// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
    
    		// @ @ search for the hex values
    		$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
    		// @ @ 0{0,7} matches '0' zero to seven times
    		$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    	}
    
    	// now the only remaining whitespace attacks are \t, \n, and \r
    	$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    	$ra = array_merge($ra1, $ra2);
    
    	$found = true; // keep replacing as long as the previous round replaced something
    	while ($found == true) {
    		$val_before = $val;
    		for ($i = 0; $i < sizeof($ra); $i++) {
    			$pattern = '/';
    			for ($j = 0; $j < strlen($ra[$i]); $j++) {
    				if ($j > 0) {
    					$pattern .= '(';
    					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
    					$pattern .= '|';
    					$pattern .= '|(&#0{0,8}([9|10|13]);)';
    					$pattern .= ')*';
    				}
    				$pattern .= $ra[$i][$j];
    			}
    			$pattern .= '/i';
    			$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
    			$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
    			if ($val_before == $val) {
    				// no replacements were made, so exit the loop
    				$found = false;
    			}
    		}
    	}
    	return $val;
    }
    
    /**
     * 把返回的数据集转换成Tree
     * @access public
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    public static function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    	// 创建Tree
    	$tree = array();
    	if(is_array($list)) {
    		// 创建基于主键的数组引用
    		$refer = array();
    		foreach ($list as $key => $data) {
    			$refer[$data[$pk]] =& $list[$key];
    		}
    		foreach ($list as $key => $data) {
    			// 判断是否存在parent
    			$parentId = $data[$pid];
    			if ($root == $parentId) {
    				$tree[] =& $list[$key];
    			}else{
    				if (isset($refer[$parentId])) {
    					$parent =& $refer[$parentId];
    					$parent[$child][] =& $list[$key];
    				}
    			}
    		}
    	}
    	return $tree;
    }
    
    /**
     * 对查询结果集进行排序
     * @access public
     * @param array $list 查询结果
     * @param string $field 排序的字段名
     * @param array $sortby 排序类型
     * asc正向排序 desc逆向排序 nat自然排序
     * @return array
     */
    public static function list_sort_by($list,$field, $sortby='asc') {
    	if(is_array($list)){
    		$refer = $resultSet = array();
    		foreach ($list as $i => $data)
    			$refer[$i] = &$data[$field];
    		switch ($sortby) {
    			case 'asc': // 正向排序
    				asort($refer);
    				break;
    			case 'desc':// 逆向排序
    				arsort($refer);
    				break;
    			case 'nat': // 自然排序
    				natcasesort($refer);
    				break;
    		}
    		foreach ( $refer as $key=> $val)
    			$resultSet[] = &$list[$key];
    		return $resultSet;
    	}
    	return false;
    }
    
    /**
     * 在数据列表中搜索
     * @access public
     * @param array $list 数据列表
     * @param mixed $condition 查询条件
     * 支持 array('name'=>$value) 或者 name=$value
     * @return array
     */
    public static function list_search($list,$condition) {
    	if(is_string($condition))
    		parse_str($condition,$condition);
    	// 返回的结果集合
    	$resultSet = array();
    	foreach ($list as $key=>$data){
    		$find   =   false;
    		foreach ($condition as $field=>$value){
    			if(isset($data[$field])) {
    				if(0 === strpos($value,'/')) {
    					$find   =   preg_match($value,$data[$field]);
    				}elseif($data[$field]==$value){
    					$find = true;
    				}
    			}
    		}
    		if($find)
    			$resultSet[]     =   &$list[$key];
    	}
    	return $resultSet;
    }
    
    // 自动转换字符集 支持数组转换
    public static function auto_charset($fContents, $from='gbk', $to='utf-8') {
    	$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    	$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    	if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
    		//如果编码相同或者非字符串标量则不转换
    		return $fContents;
    	}
    	if (is_string($fContents)) {
    		if (function_exists('mb_convert_encoding')) {
    			return mb_convert_encoding($fContents, $to, $from);
    		} elseif (function_exists('iconv')) {
    			return iconv($from, $to, $fContents);
    		} else {
    			return $fContents;
    		}
    	} elseif (is_array($fContents)) {
    		foreach ($fContents as $key => $val) {
    			$_key = self::auto_charset($key, $from, $to);
    			$fContents[$_key] = self::auto_charset($val, $from, $to);
    			if ($key != $_key)
    				unset($fContents[$key]);
    		}
    		return $fContents;
    	}
    	else {
    		return $fContents;
    	}
    }
    
    /**
     * 把一个汉字转为unicode的通用函数，不依赖任何库，和别的自定义函数，但有条件
     * 条件：本文件以及函数的输入参数应该用utf-8编码，不然要加函数转换
     * 其实亦可轻易编写反向转换的函数，甚至不局限于汉字，奇怪为什么PHP没有现成函数
     *
     * @param {string} $word 必须是一个汉字，或代表汉字的一个数组(用str_split切割过)
     * @return {string} 一个十进制unicode码，如4f60，代表汉字 “你”
     *
     * @example
     * echo "你 ".getUnicodeFromOneUTF8("你");
	 * echo "<br />";
	 * echo "好 ".getUnicodeFromOneUTF8("好");
	 * echo "<br />";
	 * echo "你好 ".getUnicodeFromOneUTF8("你好");
	 * echo "<br />";
	 * echo "你好吗 ".getUnicodeFromOneUTF8("你好吗");
	 * 你 20320
	 * 好 22909
	 * 你好 251503099357000
	 * 你好吗 4.21952182258E+21
     */
    public static function getUnicodeFromOneUTF8($word)
    {
    	//获取其字符的内部数组表示，所以本文件应用utf-8编码!
    	if (is_array( $word))
    		$arr = $word;
    	else
    		$arr = str_split($word);
    	//此时，$arr应类似array(228, 189, 160)
    	//定义一个空字符串存储
    	$bin_str = '';
    	//转成数字再转成二进制字符串，最后联合起来。
    	foreach ($arr as $value)
    		$bin_str .= decbin(ord($value));
    	//此时，$bin_str应类似111001001011110110100000,如果是汉字"你"
    	//正则截取
    	$bin_str = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/','$1$2$3', $bin_str);
    
    	//此时， $bin_str应类似0100111101100000,如果是汉字"你"
    	return bindec($bin_str);
    	//返回类似20320， 汉字"你"
    	//return dechex(bindec($bin_str));
    	//如想返回十六进制4f60，用这句
    }
    
    /**
     * 下载远程图片
     * 
     * @param $url string 运程图片网址
     * @param $filename string 下载到本地的图片名，带本地路径
     * @param $timeout int 在发起连接前等待的时间
     * @param $microtime int cURL允许执行的最长毫秒数
     * 
     * @author hughnian <hugh.nian@163.com>
     * 
     */
    public static function urlDownloadImg($url, $filename = "", $timeout = null, $microtime = null)
    {
    	if($url == '') {
    		return false; //如果 $url 为空则返回 false;
    	}
    	$ext_name = strrchr($url, '.'); //获取图片的扩展名
    	if($ext_name != '.gif' && $ext_name != '.jpg' && $ext_name != '.bmp' && $ext_name != '.png' && $ext_name != '.tmp') {
    		return false; //格式不在允许的范围
    	}
    	if($filename == '') {
    		$filename = time().$ext_name; //以时间戳另起名
    	}
    	
    	//开始下载图片
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_NOSIGNAL,1); //设置毫秒超时必须设置这个
	    if(!is_null($timeout)) curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); //在发起连接前等待的时间
	    if(!is_null($microtime)) curl_setopt($ch, CURLOPT_TIMEOUT_MS, $microtime); //设置cURL允许执行的最长毫秒数
	    curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.72 Safari/537.36");

	    $contents = curl_exec($ch);
	    $curl_errno = curl_errno($ch); //curl错误码
	    $curl_error = curl_error($ch); //curl错误信息
	    curl_close($ch);
	    
	    if($curl_errno > 0) {
	    	return false;	
	    } else if (preg_match("/404/", $contents)){
	        return false;
	    } else {
	    	$write = @fopen($filename,"w"); 
			fwrite($write,$contents); 
			fclose($write);
			$imgtag = getimagesize($filename);
			if($imgtag['mime'] == "image/jpeg" || $imgtag['mime'] == "image/png" || $imgtag['mime'] == "image/gif"){
				return $filename;
			} else {
				return false;
			}
	    }
    }
    
    /**
     * 通过curl下载线上的图片
     * 
     * @param string $url - 图片地址
     * @param int $timeout - 图片下载超时秒数
     */
    public static function curlGetImg($url, $timeout=15) {
        if(!$url) {
            return null;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOSIGNAL,1); //设置毫秒超时必须设置这个
        if(!is_null($timeout)) curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //执行超时时间
        
        //设置用户代理
        curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.72 Safari/537.36");
        
        $content = curl_exec($ch);
        $curl_errno = curl_errno($ch); //curl错误码
        $curl_error = curl_error($ch); //curl错误信息
        curl_close($ch);
        if(!$content) {
            return null;
        }
        //存储地址
        $temppath = tempnam(WEB_ROOT_DIR . '/temp', 'image');
        file_put_contents($temppath, $content);
        $imageinfo = getimagesize($temppath);
        if(!$imageinfo || !is_array($imageinfo) || !in_array($imageinfo[2], array(1,2,3,6))) {//1=>gif,2=>jpg,3=>png,6=>bmp
            @unlink($temppath);
            return null;
        }
        return $temppath;
    }
    
    /**
     * 验证手机号码
     *
     */
    public static function checkPhoneNum($phonenum)
    {
    	if(preg_match("/^1[0-9]{1}[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$phonenum)){
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * 验证邮箱
     * 
     */
    public static function checkEmail($email)
    {
    	if(preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$/', $email)){
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * 递归获取菜单树
     * 
     */
    public static function getTreeByRecu($tree, $level)
    {
    	foreach($tree as $key => $val) {
    		$arr[] = array('id'=>$val['id'], 'name' => $level . '┗' . $val['name']);
    		if(array_key_exists('suns', $val)) {
    			$arr = array_merge($arr, self::getTreeByRecu($val['suns'], $level . '　'));
    		}
    	}
    	return $arr;
    }
}