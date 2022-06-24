<?php
namespace eBaocd\Common;

class xHttp
{
    /**
     * HTTP POST json data
     *
     * @param string $url        	
     * @param jsonstring $data_string        	
     * @return array msgcode like 200,404
     *         msg return value
     */
    public static function http_post_json($url, $data_string) {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen ( $data_string ) 
        ) );
        ob_start ();
        curl_exec ( $ch );
        $return_content = ob_get_contents ();
        ob_end_clean ();
        
        $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        return array (
                'msgcode' => $return_code,
                'msg' => $return_content 
        );
    }
    /**
     *
     * @param string $url        	
     * @param array $data
     *        	key,value必须成对，不能含子array
     * @return @return array msgcode like 200,404
     *         msg return value
     */
    public static function http_post_array($url, array $data) {
        $poststr = http_build_query ( $data );
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //添加HTTP版本信息
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $poststr );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 ); // 10s执行时间
        $return_content = curl_exec ( $ch );
        $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        $return_errmsg = '';
        if (curl_errno ( $ch )) {
            $return_errmsg = curl_error ( $ch ); // 捕抓异常
        }
        curl_close ( $ch );
        
        return array (
                'msgcode' => $return_code,
                'msg' => $return_content,
                'errmsg' => $return_errmsg 
        );
    }
    public static function https_post_array($url, array $data, $isUrlEncode = true) {
        if ($isUrlEncode) {
            $poststr = http_build_query ( $data );
        } else {
            $signdata = array ();
            foreach ( $data as $k1 => $v1 ) {
                $signdata [$k1] = $k1 . '=' . $v1;
            }
            $poststr = implode ( '&', $signdata );
        }
        
        $curl = curl_init (); // 启动一个CURL会话
        curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); // 对认证证书来源的检查
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 1 ); // 从证书中检查SSL加密算法是否存在
        curl_setopt ( $curl, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] ); // 模拟用户使用的浏览器
        curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
        curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
        curl_setopt ( $curl, CURLOPT_POST, 1 ); // 发送一个常规的Post请求
        curl_setopt ( $curl, CURLOPT_POSTFIELDS, $poststr ); // Post提交的数据包
        curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
        curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 显示返回的Header区域内容
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
        
        $return_content = curl_exec ( $curl );
        $return_code = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
        $return_errmsg = '';
        if (curl_errno ( $curl )) {
            $return_errmsg = curl_error ( $curl ); // 捕抓异常
        }
        curl_close ( $curl );
        
        return array (
                'msgcode' => $return_code,
                'msg' => $return_content,
                'errmsg' => $return_errmsg 
        );
    }
}
?>