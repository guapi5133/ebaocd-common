<?php
namespace eBaocd\Common;

use Apps\Config\ComStatic;
use Apps\Config\Config;

class xFun
{
    /**
     * 时间戳格式化
     *
     * @param $timestamp
     *
     * @return false|string
     */
    public static function formatToDate($timestamp)
    {
        $time = time() - $timestamp;
        if ($time < 60)
        {
            $formatime = '刚刚';
        }
        elseif ($time < 60 * 60)
        {
            $min       = floor($time / 60);
            $formatime = $min . '分钟前';
        }
        elseif ($time < 60 * 60 * 24)
        {
            $h         = floor($time / (60 * 60));
            $formatime = $h . '小时前';
        }
        else
        {
            $formatime = date('Y') == date('Y', $timestamp) ? date('m月d日 H:i', $timestamp) : date('Y年m月d日 H:i', $timestamp);
        }

        return $formatime;
    }

    /**
     * (时间戳)人性格式化
     *
     * @param int $timestamp 时间戳
     *
     * @return false|string
     */
    public static function format2Day($timestamp)
    {
        if (!is_numeric($timestamp))
        {
            return '';
        }

        $from_day = date('Y-m-d', $timestamp);
        $day      = date('Y-m-d');

        $format = '';

        if ($from_day == $day)
        { /* 今天 */
            $format = date('H:i', $timestamp);
        }
        else
        {
            $yDay = date('Y-m-d', strtotime('-1 day'));

            if ($from_day == $yDay)
            { /* 昨天 */
                $format = '昨天';
            }
            else
            {
                $from_year = substr($from_day, 0, 4);
                $year      = substr($day, 0, 4);

                if ($from_year == $year)
                { /* 今年 */
                    $format = substr($from_day, 5);
                }
                else
                { /* 过去 */
                    $format = $from_day;
                }
            }
        }

        if (!is_string($format))
        {
            return '';
        }
        else
        {
            return $format;
        }
    }

    public static function real_ip()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], 'unknown'))
        {
            $onlineip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], 'unknown'))
        {
            $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif ($_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
        {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
        {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';

        return $onlineip;
    }

    public static function object_to_array($param)
    {
        //TODO 未测试
        //return json_decode(json_encode($param), true);
        if (is_object($param))
        {
            $param = (array)$param;
        }

        if (is_array($param))
        {
            foreach ($param as $key => $value)
            {
                $param[$key] = self::object_to_array($value);
            }
        }

        return $param;
    }

    public static function is_post()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') ? TRUE : FALSE;
    }

    public static function is_ajax()
    {
        return self::reqnum("ajax") == 1 || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? TRUE : FALSE;
    }

    public static function is_email($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public static function is_num($obj, $init = 0)
    {
        return isset ($obj) && is_numeric($obj) ? $obj : $init;
    }

    public static function is_str($obj, $init = '')
    {
        return isset ($obj) ? trim($obj) : $init;
    }

    public static function is_money($num, $init = 0)
    {
        $num2 = filter_var($num, FILTER_VALIDATE_FLOAT);
        if ($num2 === FALSE)
        {
            return $init;
        }
        else
        {
            return $num;
        }
    }

    public static function is_mobile($param)
    {
        return preg_match('/^1[3-9]\d{9}$/', $param);
    }

    public static function is_url($param)
    {
        return (bool)filter_var($param, FILTER_VALIDATE_URL);
    }

    public static function chinese($param, $min, $max)
    {
        $preg = "/^[\x{4e00}-\x{9fa5}0-9a-zA-Z]{{$min},{$max}}$/u";

        return preg_match($preg, $param);
    }

    public static function is_tel($param)
    {
        return preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/', $param);
    }

    //身份证
    public static function is_card_id($param)
    {
        return preg_match('/^\d{18}$/', $param);
    }

    //护照
    public static function is_passport($param)
    {
        return preg_match('/^\w{5,17}$/', $param);
    }

    public static function is_password($param)
    {
        return preg_match('/^[a-zA-Z0-9]{6,20}$/', $param);
    }

    public static function req($name)
    {
        return isset($_REQUEST [$name]) ? $_REQUEST [$name] : '';
    }

    public static function reqmoney($name, $init = 0)
    {
        $num  = self::req($name);
        $num2 = preg_match('/^(\d+)(\.\d+)?$/', $num);
        if (!empty($num2) && $num2 > 0)
        {
            return $num;
        }
        else
        {
            return $init;
        }
    }

    public static function reqnum($name, $init = 0, $code = 0)
    {
        $value = self::is_num(self::req($name), $init);

        return $code > 0 && !self::isEmpty($value, $init) ? self::output($code) : $value; //如果带有报错码并且此值为空时报错
    }

    public static function reqabsnum($name, $init)
    {
        return \abs(self::is_num(self::req($name), $init));
    }

    public static function reqstr($name, $init = '', $code = 0)
    {
        $value = self::is_str(self::req($name), $init);

        return $code > 0 && !self::isEmpty($value, $init) ? self::output($code) : $value; //如果带有报错码并且此值为空时报错
    }

    public static function reqdate($name, $init = '')
    {
        $rea = self::is_str(self::req($name), $init);

        if ($rea != '')
        {
            $rea = strtotime($rea);
            if ($rea !== FALSE)
            {
                $rea = date('Y-m-d', $rea);
            }
            else
            {
                $rea = $init;
            }
        }
        else
        {
            $rea = $init;
        }

        return $rea;
    }

    public static function reqarray($name, $init = array())
    {
        $array = self::req($name);
        if (!empty($array))
        {
            if (!is_array($array))
            {
                settype($array, "array");
            }
        }
        else
        {
            $array = $init;
        }

        return $array;
    }

    public static function reqnumarray($name, $init = array(), $initnum = 0)
    {
        $arr = self::reqarray($name, $init);

        foreach ($arr as $k1 => &$v1)
        {
            $v1 = self::is_num($v1, $initnum);
        }

        return $arr;
        //var_dump($arr);
    }

    public static function ShowPageArr($CurrentPage, $totalnumber, $maxperpage)
    {
        $n   = ceil($totalnumber / $maxperpage);
        $rtn = array('nowtxt' => '', 'pagenum' => array(), 'prepage' => '0', 'nxtpage' => '0', 'allpage' => 0);
        if ($totalnumber == 0)
        {
            $rtn['nowtxt'] = '第0条/共0条';

            return $rtn;
        }
        $rtn['allpage'] = $n;
        $start          = ($CurrentPage - 1) * $maxperpage + 1;
        $end            = min($start + $maxperpage, $totalnumber);

        $rtn['nowtxt'] = '第' . $start . '-' . $end . '条/共' . $totalnumber . '条';
        $numall        = min($n, 9);
        $numstart      = 1;
        if ($CurrentPage > 9)
        {
            $numall   = $CurrentPage + 5;
            $numstart = $CurrentPage - 4;
        }
        //echo $numall;
        for (; $numstart < $numall; $numstart++)
        {
            $rtn['pagenum'][] = $numstart;
        }
        if ($CurrentPage > 1)
        {
            $rtn['prepage'] = $CurrentPage - 1;
        }
        if ($CurrentPage >= 1 && $CurrentPage < $n)
        {
            $rtn['nxtpage'] = $CurrentPage + 1;
        }

        return $rtn;
    }

    public static function ShowPage($sfilename, $CurrentPage, $totalnumber, $maxperpage, $ShowTotal = TRUE, $ShowAllPages = TRUE, $strUnit = "条", $ylnum = 10)
    {
        $n       = ceil($totalnumber / $maxperpage);
        $strTemp = $strUrl = '';

        $n = max($CurrentPage, $n);

        $strTemp = "<table align='center'><tr><td>";
        if ($ShowTotal)
        {
            $strTemp .= "共 <FONT COLOR=red><b>" . $totalnumber . "</b></FONT> " . $strUnit . "&nbsp;&nbsp;";
        }
        $strUrl = $sfilename;//JoinChar(sfilename);
        $pos    = strpos($strUrl, '?');
        if ($pos === FALSE)
        {
            $strUrl .= '?1';
        }

        if ($CurrentPage < 2)
        {
            $strTemp .= "首页 上一页&nbsp;";
        }
        else
        {
            $strTemp .= "<a href='" . $strUrl . "&page=1'>首页</a>&nbsp;&nbsp;";
            $strTemp .= "<a href='" . $strUrl . "&page=" . ($CurrentPage - 1) . "'>上一页</a>&nbsp;&nbsp;";
        }

        if ($n - $CurrentPage < 1)
        {
            $strTemp .= "下一页 &nbsp;尾页";
        }
        else
        {
            $strTemp .= "<a href='" . $strUrl . "&page=" . ($CurrentPage + 1) . "'>下一页</a>&nbsp;&nbsp;";
            $strTemp .= "<a href='" . $strUrl . "&page=" . $n . "'>尾页</a>";
        }
        $strTemp .= "&nbsp;页次：<strong><font color=red>" . $CurrentPage . "</font>/" . $n . "</strong>页 ";
        $strTemp .= "&nbsp;<b>" . $maxperpage . "</b>" . $strUnit . "/页";
        if ($ShowAllPages)
        {

            if ($n > 50)
            {
                $n = $CurrentPage + $ylnum;
            }
            $strTemp .= "&nbsp;转到：<select class='col-md-2 float-right' name='page' size='1' style='width:90px;float:right;' onchange=\"javascript:document.location.href='" . $strUrl . "&page='+this.options[this.selectedIndex].value; \">";
            for ($i = 1; $i <= $n; $i++)
            {
                $strTemp .= "<option value='" . $i . "'";
                if ($CurrentPage == $i)
                {
                    $strTemp .= " selected ";
                }
                $strTemp .= ">第" . $i . "页</option>";
            }
            $strTemp .= "</select>";
        }
        $strTemp .= "</td></tr></table>";

        return $strTemp;
    }

    /*
	 * 获得字符串长度：中文、全角字母数字算1，半角字母数字算0.5
	 */
    public static function zhstrlen($str)
    {
        $str = strip_tags($str);

        return (strlen($str) + mb_strlen($str, 'UTF8')) / 4;
    }

    /**
     * 字符串截取
     *
     * @param string  $srcstr
     *            源字符串
     * @param number  $len
     *            汉字的长度
     * @param boolean $once
     *            true只取一次，否则取多次，以<br />分隔，即强制换行
     *
     * @return string
     */
    public static function msubstr($srcstr, $len, $once = FALSE)
    {
        $str = strip_tags($srcstr);
        mb_internal_encoding("UTF-8");
        $srcbytelen = strlen($str);
        $bytelen    = $len * 3; // UTF-8汉字占3个字节
        $tmpstr     = '';
        for ($start = 0; $start <= $srcbytelen; $start += $bytelen)
        {
            $tmpstr .= mb_strcut($str, $start, $bytelen);
            if ($once)
            {
                break;
            }
            $tmpstr .= '<br />';
        }

        return $tmpstr;
    }

    /**
     * 文本框内带有格式的输入替换为HTML代码
     *
     * @param string $str 输入字符串
     *
     * @return string
     */
    public static function fmt_txt_html($str)
    {
        $str = str_ireplace(" ", "&nbsp;", $str);
        $str = str_ireplace("　", "&nbsp;&nbsp;", $str);
        $str = nl2br($str);

        return $str;
    }

    /**
     * 带有格式的输入替换为文本框中的内容
     *
     * @param string $str 输入字符串
     *
     * @return string
     */
    public static function fmt_html_txt($str)
    {
        $str2 = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $str);
        $str2 = str_ireplace("&nbsp;", " ", $str2);

        return $str2;
    }

    //将字符串格式化成数组
    public static function fmt_str_arr($str, $strsplit = ",")
    {
        $rtn = array();
        $str = trim($str, $strsplit);
        if ($str != '')
        {
            $rtn = explode($strsplit, $str);
        }
        else
        {
            $rtn = [];
        }

        return $rtn;
    }

    //将数组格式化成字符串
    public static function fmt_arr_str($arr = array(), $strsplit = ",", $isnum = TRUE)
    {
        $str = '';
        foreach ($arr as $k => $v)
        {
            $v = trim($v);
            if ($v == '')
            {
                continue;
            }
            if ($isnum)
            {
                if (is_numeric($v))
                {
                    $str .= $v . $strsplit;
                }
            }
            else
            {
                $str .= $v . $strsplit;
            }
        }
        if ($str != '')
        {
            $str = $strsplit . $str;
        }

        return $str;
    }

    //遍历数组，将所有数字型转换成字符串型  $float 小数点位数
    //php7下，数字以json形式输出时，较小机率会输出一个很长的浮点数，转换成字符串可以解决，同时也是为了兼容 强类型
    public static function fmt_num_str($param, $float = 1)
    {
        if (is_array($param))
        {
            foreach ($param as $i => $v)
            {
                $param[$i] = self::fmt_num_str($v);
            }

            return $param;
        }

        if (is_numeric($param))
        {
            //return is_float($param) ? sprintf('%.' . $float . 'f', $param) : strval($param);
            return sprintf('%s', $param);
        }

        return $param;
    }

    //生成随机字母和数字
    public static function randstr($len = 8)
    {
        $res = '';
        $str = 'abcdefjhijklmnpqrstuvwxyzABCDEFJHIJKLMNPQRSTUVWXYZ1234567890';
        $max = strlen($str);
        for ($i = 1; $i <= $len; $i++)
        {
            $strpos = \mt_rand(0, $max - 1);
            $res    .= \substr($str, $strpos, 1);
        }

        return $res;
    }

    //输出错误信息  error 0为正常，大于0错误
    public static function output($error, $data = NULL, $ifweb = FALSE)
    {
        $msg = array(
            0   => 'SUCCESS',
            998 => '请登录',

            101 => '不合法的请求',
            102 => '无权限',
            103 => '网络错误',
            104 => '请检查您的系统时间',
            105 => '参数错误',
            106 => '错误的执行方式',
            107 => '未找到相关信息',
            108 => '操作失败',
            201 => '手机号格式不正确',
        );

        if (is_numeric($error))
        {
            if (isset($msg[$error]))
            {
                if ($error == 998 && !empty($data) && is_string($data))
                {
                    $info = $data;
                    $data = NULL;
                }
                else
                {
                    $info = $msg[$error];
                }
            }
            else
            {
                $info  = 'FAIL';
                $error = 999; //通用报错码，用于自定义报错内容  ['error'=> 999, 'message' => 操作超时, 'data' => []]
            }
        }
        else
        {
            $info  = $error;
            $error = 999;
        }

        if ($ifweb) //如果来自web（模板），只返回错误信息
        {
            return $info;
        }

        $arr = [
            'code' => $error,
            'msg'  => $info,
            'data' => !empty($data) ? self::fmt_num_str($data) : $data
        ];

        exit(json_encode($arr));
    }

    public static function toSize($byte, $pre = 1)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;

        $res = 0;
        if ($byte >= $gb)
        {
            $res = round($byte / $gb, $pre) . 'GB';
        }
        elseif ($byte >= $mb)
        {
            $res = round($byte / $mb, $pre) . 'MB';
        }
        elseif ($byte >= $kb)
        {
            $res = round($byte / $kb, $pre) . 'KB';
        }
        else
        {
            $res = $byte . 'B';
        }

        return $res;
    }

    /**
     * 全球唯一标识
     * @return string
     */
    public static function guid()
    {
        if (function_exists('com_create_guid'))
        {
            return trim(com_create_guid(), '{}');
        }
        else
        {
            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        }
    }

    /**
     * 解析URL，得到controller和acton，并注入到全局配置文件中
     *
     * @param $controller
     * @param $action
     *
     * @return bool
     */
    public static function urlParse(&$controller, &$action)
    {
        global $APP_G;
        $uri     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri     = (!$uri || $uri == '/' || $uri == '/index.php') ? 'index/index' : $uri;
        $uri     = trim($uri, '/');
        $uri_arr = explode('/', $uri);
        //@list($controller, $action) = $uri_arr;

        $controller = $uri_arr[0] ?? 'index';
        $action     = $uri_arr[1] ?? 'index';

        global $APP_G;
        $APP_G['controller'] = $controller;
        $APP_G['action']     = $action;
        $APP_G['url']        = "/$controller/$action";

        return TRUE;
    }

    /**
     * 用户操作日志（改库）
     *
     * @param string $info
     *
     * @return true
     */
    public static function userActionLog($info = '', $type = 'access')
    {
        //目录构造 /data/store/logs/php/$type_log/
        $path = LOG_PATH . DS . $type . '_log';
        $path .= DS;

        if (!is_dir($path))
        {
            self::createDir($path, 755);
        }

        $filename   = $path . date('Y-m-d') . '.txt';
        $controller = self::getGlobalConfig('controller');
        $action     = self::getGlobalConfig('action');
        $userInfo   = self::getGlobalConfig('userinfo');
        $channel    = self::getGlobalConfig('from_channnel');
        $name       = $userInfo['user_name'] ?? '';
        $id         = $userInfo['user_id'] ?? 0;

        //内容
        $str = date('Y-m-d H:i:s') . ' ';
        $str .= '用户名为：' . $name . '（id：' . $id . "），通过渠道：$channel 访问了 ";
        $str .= $controller . '/' . $action;
        if (!empty($info))
        {
            $str .= '并且执行了以下操作：' . $info;
        }

        file_put_contents($filename, $str, FILE_APPEND);

        return TRUE;
    }

    /**
     * 返回全局配置信息
     *
     * @param string $name 为空则返回所有配置
     *
     * @return string | array
     */
    public static function getGlobalConfig($name = '')
    {
        global $APP_G;
        if (empty($name))
        {
            return $APP_G;
        }

        $name = explode('.', $name);

        $dd = $APP_G;
        foreach ($name as $value)
        {
            if (!isset($dd[$value]))
            {
                return NULL;
            }

            $dd = $dd[$value];
        }

        return $dd;
    }

    /**
     * 改写全局配置信息
     *
     * @param string $name 配置名
     *
     * @return bool
     */
    public static function setGlobalConfig($name, $value)
    {
        global $APP_G;

        return !isset($APP_G[$name]) ? FALSE : $APP_G[$name] = $value;
    }

    /**
     * 创建一个目录（含递归）
     *
     * @param string $pathname 完整目录名
     * @param int    $chmod    权限数字
     *
     * @return bool
     */
    public static function createDir($pathname, $chmod = 0777)
    {
        if (is_dir($pathname))
        {
            return TRUE;
        }

        mkdir($pathname, $chmod, TRUE);
        chmod($pathname, $chmod);

        /*if (strtolower(self::getOs()) == 'linux')
        {
            $command = "mkdir -p $pathname";
            exec($command);

            $command = "chmod -R $chmod $pathname";
            exec($command);
        }
        else
        {
            mkdir($pathname, $chmod, TRUE);
        }*/

        return is_dir($pathname);
    }

    /**
     * 将秒数换算成XX小时XX分XX秒
     *
     * @param int $seconds 秒数
     *
     * @return string XX小时XX分XX秒
     */
    public static function secToHourMinSec($seconds, $format = '')
    {
        if ($seconds > 3600)
        {
            $format  = empty($format) ? '%M:%S' : $format;
            $hours   = intval($seconds / 3600);
            $minutes = $seconds % 3600;
            $time    = $hours . ":" . gmstrftime($format, $minutes);
        }
        else
        {
            $format = empty($format) ? '%H:%M:%S' : $format;
            $time   = gmstrftime($format, $seconds);
        }

        return $time;
    }

    /**
     * 返回指定日期的 0 点和 23点 时间戳
     *
     * @param string $date 2019-09-04
     *
     * @return int 时间戳
     */
    public static function getStartEndTime($date = '', $type = '')
    {
        $unixtime = !empty($date) ? strtotime($date) : time();

        switch (strtolower($type))
        {
            case "start" :
                return strtotime(date('Y-m-d 00:00:00', $unixtime));
            break;

            case "end" :
                return strtotime(date('Y-m-d 23:59:59', $unixtime));
            break;

            default :
                return [
                    'stime' => strtotime(date('Y-m-d 00:00:00', $unixtime)),
                    'etime' => strtotime(date('Y-m-d 23:59:59', $unixtime)),
                ];
            break;
        }

        return TRUE;
    }

    /**
     * 返回若干天前的时间戳
     *
     * @param        $day
     * @param string $type
     *
     * @return array|bool|int
     */
    public static function getBeforeTime($day, $type = '')
    {
        $date = date('Y-m-d', strtotime("-$day day"));

        return self::getStartEndTime($date, $type);
    }

    /**
     * 返回一段时间内的开始和结束时间
     * @param string $range
     * @return int 时间戳
     */
    public static function getRangeTime($range = 'day')
    {
        switch ($range)
        {
            case 'day' :
                return xFun::getStartEndTime();
            break;

            case 'week' :
                $s = xFun::getStartEndTime(date('Y-m-d', strtotime('last Monday', time())),'start');
                $e = xFun::getStartEndTime(date('Y-m-d', strtotime('Sunday', time())),'end');

                return ['stime' => $s, 'etime' => $e];
            break;

            case 'month' :
                $s = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $e = mktime(23, 59, 59, date('m'), date('t'), date('Y'));

                return ['stime' => $s, 'etime' => $e];
            break;
        }
    }

    /**
     * 获取客户端操作系统信息
     *
     * @return string
     */
    public static function getOs()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT']))
        {
            $OS = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i', $OS))
            {
                $OS = 'Windows';
            }
            elseif (preg_match('/mac/i', $OS))
            {
                $OS = 'MAC';
            }
            elseif (preg_match('/linux/i', $OS))
            {
                $OS = 'Linux';
            }
            elseif (preg_match('/unix/i', $OS))
            {
                $OS = 'Unix';
            }
            elseif (preg_match('/bsd/i', $OS))
            {
                $OS = 'BSD';
            }
            else
            {
                $OS = 'Other';
            }

            return $OS;
        }
        else
        {
            return "";
        }
    }

    public function createRedisRandKey($value = NULL, $outtime = 3600, $len = 8)
    {
        $key   = self::randstr($len);
        $redis = xRedis::init();

        //如果键值存在，递归调用
        if ($redis->exists($key))
        {
            self::createRedisRandKey($value, $len);
        }

        if (empty($value))
        {
            return $key;
        }

        return is_array($value) ? $redis->setex($key, $outtime, json_encode($value)) : $redis->setex($key, $outtime);
    }

    public static function curl_http_file($url, $file)
    {
        $ch   = curl_init();//初始化
        $data = [
            'key'  => self::getCurlFileKey(),
            'file' => curl_file_create($file['tmp_name'], $file['type'], $file['name'])
        ];
        curl_setopt($ch, CURLOPT_URL, $url);//访问的URL
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);//设置超时
        curl_setopt($ch, CURLOPT_HEADER, FALSE);//设置不需要头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//只获取页面内容，但不输出
        curl_setopt($ch, CURLOPT_POST, TRUE);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//提交的数据
        $output = curl_exec($ch);//执行请求
        if (!$output)
        {
            $errno = curl_errno($ch);//错误码
            $error = curl_error($ch);//错误信息
            //echo "CURL错误码：$errno</br>CURL错误信息：$error<br><a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html' target='_blank'>错误原因查询</a>";
            $error = "CURL错误码：$errno-CURL错误信息：$error";
            self::write_log($error, 'error_curl_log');
        }
        curl_close($ch);//关闭curl，释放资源

        return $output;
    }

    public static function curl_http_request($url, $data = NULL, $header = FALSE, $cert = FALSE, $ssl = FALSE)
    {
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);//访问的URL
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_HEADER, FALSE);//设置不需要头信息
        //设置头信息
        if ($header)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//只获取页面内容，但不输出
        if ($ssl)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);//https请求 不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//https请求 严格校验
        }
        else
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//https请求 不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//https请求 不验证HOST
        }
        //设置证书
        if ($cert)
        {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');//默认格式为PEM，可以注释
            curl_setopt($ch, CURLOPT_SSLCERT, '');//cert.pem文件
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');//默认格式为PEM，可以注释
            curl_setopt($ch, CURLOPT_SSLKEY, '');//key.pem文件
        }
        //post提交
        if (!is_null($data))
        {
            curl_setopt($ch, CURLOPT_POST, TRUE);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//提交的数据
        }
        $output = curl_exec($ch);//执行请求
        if (!$output)
        {
            $errno = curl_errno($ch);//错误码
            $error = curl_error($ch);//错误信息
            //echo "CURL错误码：$errno</br>CURL错误信息：$error<br><a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html' target='_blank'>错误原因查询</a>";
            $error = "CURL错误码：$errno-CURL错误信息：$error";
            self::write_log($error, 'error_curl_log');
        }
        curl_close($ch);//关闭curl，释放资源

        return $output;
    }

    //get请求
    public static function curl_http_get($url, $data, $header, $method = 'GET')
    {
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);//访问的URL
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_HEADER, FALSE);//设置不需要头信息
        //设置头信息
        if ($header)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//只获取页面内容，但不输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//提交的数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $output = curl_exec($ch);//执行请求
        if (!$output)
        {
            $errno = curl_errno($ch);//错误码
            $error = curl_error($ch);//错误信息
            $error = "CURL错误码：$errno-CURL错误信息：$error";
            self::write_log($error, 'error_curl_log');
        }
        curl_close($ch);//关闭curl，释放资源

        return $output;
    }

    //不中断程序的file_get_contents请求
    public static function file_get_contents_get($url)
    {
        $context = stream_context_create(['http' => ['ignore_errors' => TRUE, 'method' => 'GET']]);

        return file_get_contents($url, FALSE, $context);
    }

    public static function curl_async_request($url, $param = NULL, $header = NULL, $timeout = 50)
    {
        //if (!is_url($url)) return false;

        //设置curl
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);//请求URL
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);//设置超时,毫秒一定要设置这个
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout); //设置超时,毫秒,不建议低于50ms。cURL 7.16.2中被加入，从PHP 5.2.3起可使用
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置输出头信息 默认0
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//只获取页面内容，但不输出
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//是否验证对等方证书的真实性 1.是(默认) 0.否
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//是否验证证书适用于已知的服务器 2.是(默认) 0.否
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');//post请求
        if (!is_null($header))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }//请求输入头信息
        if (!is_null($param))
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }//请求参数

        //执行curl
        $output = curl_exec($ch);

        //关闭curl
        curl_close($ch);

        return $output;
    }

    public static function getCurlFileKey()
    {
        $time = time();
        $key  = "created=$time&sign=" . self::encrypt($time, $time);

        return base64_encode($key);
    }

    public static function encrypt($password, $salt)
    {
        return md5(md5($password) . $salt);
    }

    public static function encryptPass($password)
    {
        $salt = self::getGlobalConfig('project') ?: $password;

        return md5(sha1($password . $salt));
    }

    public static function is_base64($param)
    {
        return $param == base64_encode(base64_decode($param));
    }

    public static function isEmoji($str)
    {
        $callback = function ($matches) {
            return strlen($matches[0]) >= 4 ? 1 : 0;
        };
        $str      = preg_replace_callback('/./u', $callback, $str);

        return preg_match('/1/', $str);
    }

    public static function getSeparator($str, $separator = ',', $index = NULL)
    {
        $arr = explode($separator, $str);

        return is_null($index) ? end($arr) : $arr[$index] ?? '';
    }

    public static function interval_time($micro = TRUE)
    {
        if ($micro)
        {
            $start = $_SERVER['REQUEST_TIME_FLOAT'];
            $end   = microtime(TRUE);
        }
        else
        {
            $start = $_SERVER['REQUEST_TIME'];
            $end   = time();
        }

        return $end - $start;
    }

    //获取当前请求的所有请求头信息
    public static function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    public static function write_log($data = '', $dir = 'record_log', $filename = '')
    {
        $path = LOG_PATH . $dir . DS;
        if (!is_dir($path))
        {
            mkdir($path, 0755, TRUE);
        }

        $filename = empty($filename) ? $path . date('Y-m-d') . '.log' : $path . $filename;
        file_put_contents($filename, self::array_to_str($data) . "\r\n", FILE_APPEND);
    }

    public static function write($data = '', $dir = 'record_log')
    {
        $path = LOG_PATH . $dir . DS;
        if (!is_dir($path))
        {
            mkdir($path, 0755, TRUE);
        }

        $filename = $path . date('Y-m-d') . '.log';
        file_put_contents($filename, json_encode($data, 320) . PHP_EOL, FILE_APPEND);
    }

    //获取日志路径
    public static function getLogPath($dir = 'record_log', $create = TRUE)
    {
        $path = LOG_PATH . DS . $dir . DS . SERVER_ENV . DS;
        if ($create)
        {
            self::createDir($path);
        }

        return $path;
    }

    public static function array_to_str($param = [], $encode = FALSE)
    {
        if (!is_array($param))
        {
            return $param;
        }

        if ($encode)
        {
            return http_build_query($param);
        }

        $str = '';
        foreach ($param as $key => $value)
        {
            $str .= $key . '=' . self::array_to_str($value) . '&';
        }
        $str = trim($str, '&');

        return $str;
    }

    public static function fn_safe($str_string)
    {
        //直接剔除
        $_arr_dangerChars = array(
            "|",
            ";",
            "$",
            "@",
            "+",
            "\t",
            "\r",
            "\n",
            ",",
            "(",
            ")",
            PHP_EOL //特殊字符
        );

        //正则剔除
        $_arr_dangerRegs = array(
            /* -------- 跨站 --------*/

            //html 标签
            "/<(script|frame|iframe|bgsound|link|object|applet|embed|blink|style|layer|ilayer|base|meta)\s+\S*>/i",

            //html 属性
            "/on(afterprint|beforeprint|beforeunload|error|haschange|load|message|offline|online|pagehide|pageshow|popstate|redo|resize|storage|undo|unload|blur|change|contextmenu|focus|formchange|forminput|input|invalid|reset|select|submit|keydown|keypress|keyup|click|dblclick|drag|dragend|dragenter|dragleave|dragover|dragstart|drop|mousedown|mousemove|mouseout|mouseover|mouseup|mousewheel|scroll|abort|canplay|canplaythrough|durationchange|emptied|ended|error|loadeddata|loadedmetadata|loadstart|pause|play|playing|progress|ratechange|readystatechange|seeked|seeking|stalled|suspend|timeupdate|volumechange|waiting)\s*=\s*(\"|')?\S*(\"|')?/i",

            //html 属性包含脚本
            "/\w+\s*=\s*(\"|')?(java|vb)script:\S*(\"|')?/i",

            //js 对象
            "/(document|location)\s*\.\s*\S*/i",

            //js 函数
            "/(eval|alert|prompt|msgbox)\s*\(.*\)/i",

            //css
            "/expression\s*:\s*\S*/i",

            /* -------- sql 注入 --------*/

            //显示 数据库 | 表 | 索引 | 字段
            "/show\s+(databases|tables|index|columns)/i",

            //创建 数据库 | 表 | 索引 | 视图 | 存储过程 | 存储过程
            "/create\s+(database|table|(unique\s+)?index|view|procedure|proc)/i",

            //更新 数据库 | 表
            "/alter\s+(database|table)/i",

            //丢弃 数据库 | 表 | 索引 | 视图 | 字段
            "/drop\s+(database|table|index|view|column)/i",

            //备份 数据库 | 日志
            "/backup\s+(database|log)/i",

            //初始化 表
            "/truncate\s+table/i",

            //替换 视图
            "/replace\s+view/i",

            //创建 | 更改 字段
            "/(add|change)\s+column/i",

            //选择 | 更新 | 删除 记录
            "/(select|update|delete)\s+\S*\s+from/i",

            //插入 记录 | 选择到文件
            "/insert\s+into/i",

            //sql 函数
            "/load_file\s*\(.*\)/i",

            //sql 其他
            "/(outfile|infile)\s+(\"|')?\S*(\"|')/i",
        );

        $_str_return = $str_string;
        //$_str_return = urlencode($_str_return);

        foreach ($_arr_dangerChars as $_key => $_value)
        {
            $_str_return = str_ireplace($_value, "", $_str_return);
        }

        foreach ($_arr_dangerRegs as $_key => $_value)
        {
            $_str_return = preg_replace($_value, "", $_str_return);
        }

        $_str_return = htmlentities($_str_return, ENT_QUOTES, "UTF-8", TRUE);

        return $_str_return;
    }

    /**
     * 返回文件物理地址
     * @param string $filename // upload/img/xxx/xxx.jpg
     * @return string fullfilename
     */
    public static function getAssetPath($filename)
    {
        return ASSET_DIR . $filename;
    }

    //给资源加上域名前缀
    public static function getAssetUrl($url)
    {
        return !empty($url) ? DOMAIN_ASSET . '/' . $url : '';
    }

    //文件是否存在以及扩展名验证。文件存在则返回完整文件名
    public static function fileVerify(&$filename, array $ext = [])
    {
        $filename = self::getAssetPath($filename);
        if (!file_exists($filename))
        {
            return FALSE;
        }

        if (!empty($ext))
        {
            $fileInfo = pathinfo($filename);
            if (!in_array($fileInfo['extension'], $ext))
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * 下载文件
     *
     * @param $file
     * @param $filename
     */
    public static function downFile($file, $filename)
    {
        // 文件名乱码问题
        $charset         = 'utf-8';
        $encode_filename = rawurlencode($filename);

        $UA = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/(MSIE|Trident|Edge)/i", $UA))
        {
            $attachmentHeader = "=\"{$encode_filename}\"; charset={$charset}";
        }
        elseif (preg_match("/Firefox/i", $UA))
        {
            $attachmentHeader = '*="utf8\'\'' . $filename . '"';
        }
        else
        {
            $attachmentHeader = "=\"{$filename}\"";
        }

        header("Content-Type: application/force-download");
        header('Content-Type: application/octet-stream; charset=utf-8');
        header('Content-Disposition: attachment; filename' . $attachmentHeader);
        header('Content-Transfer-Encoding: binary');
        readfile($file);

        return TRUE;

        header('Content-Type: application/octet-stream; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');//下载文件名

        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $file);//访问的URL
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);//设置超时
        curl_setopt($ch, CURLOPT_HEADER, FALSE);//设置不需要头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//只获取页面内容，但不输出
        $output = curl_exec($ch);//执行请求
        if (!$output)
        {
            $errno = curl_errno($ch);//错误码
            $error = curl_error($ch);//错误信息
            echo "CURL错误码：$errno</br>CURL错误信息：$error<br><a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html' target='_blank'>错误原因查询</a>";
        }
        curl_close($ch);//关闭curl，释放资源
    }



    /**
     * 根据参数创建token
     *
     * @param $param
     *
     * @return string
     */
    public static function create_curl_token($param)
    {
        $str = self::array_to_str($param);

        return self::encrypt($str, $str);
    }

    /**
     * 判断是否为空，不为空返回变量本身，为空返回默认值
     *
     * @param mixed $value
     * @param null  $default
     *
     * @return mixed|null
     */
    public static function isEmpty($value, $default = NULL)
    {
        return !empty($value) ? $value : $default;
    }

    public static function create_curl_header($system)
    {
        //本地环境
        $account = xFun::getGlobalConfig("header.account.$system.account");
        $header  = ['account' => $account, 'created' => time(), 'nonce' => substr(md5(xFun::guid()), 0, 8)];

        //sign和token
        $header['sign']  = self::createSign($header, $account);
        $header['token'] = xFun::reqstr('token');

        //字符串
        $str  = self::array_to_str($header);
        $auth = base64_encode($str);

        return ['authorization: ' . $auth];
    }

    public static function createSign(array $param, string $private): string
    {
        ksort($param);
        $str = '';
        foreach ($param as $key => $value)
        {
            if (!in_array($key, ['sign', 'token']))
            {
                $str .= "$key=$value&";
            }
        }
        $str = trim($str, '&');

        return md5(md5($str) . '&key=' . md5($private));
    }

    public static function create_nonce_str(int $length = 32): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str   = '';
        for ($i = 0; $i < $length; $i++)
        {
            $index = mt_rand(0, strlen($chars) - 1);
            $str   .= $chars[$index];
        }

        return $str;
    }

    //返回一个随机资源文件名
    public static function create_asset_filename($ext = '.png')
    {
        return md5(mt_rand(10000, 99999) . self::guid()) . $ext;
    }

    //对数组的每一条key进行重新命名
    public static function renameTheKey($array_list, $key_name, $append = FALSE,$filed = '') //相同的key是覆盖还是追加，append为真是追加
    {
        $list = [];
        foreach ($array_list as $value)
        {
            if (!isset($value[$key_name]))
            {
                return FALSE;
            }

            $append ? $list[$value[$key_name]][] = (!empty($filed) ? $value[$filed]:$value) : $list[$value[$key_name]] = (!empty($filed) ? $value[$filed]:$value);
        }

        return $list;
    }
}

?>
