<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 3:00 PM
 */
namespace IO\Github\PumpkinJob\Http;

use IO\Github\PumpkinJob\Exceptions\PumpkinJobException;

class CurlManager {
    private $_is_temp_cookie = false;
    private $_header;
    private $_body;
    private $_ch;
    private $_proxy;
    private $_proxy_port;
    private $_proxy_type = 'HTTP'; // or SOCKS5
    private $_proxy_auth = 'BASIC'; // or NTLM
    private $_proxy_user;
    private $_proxy_pass;
    protected $_cookie;
    protected $_options;

    public static function getInstance($options = array()) {
        return new self($options);
    }

    public function __construct($options = array()) {
        $defaults = array();
        $defaults ['temp_root'] = sys_get_temp_dir();
        $defaults ['user_agent'] = 'yidao-uc/1.0';
        $defaults ['timeout_ms'] = 3000 ;
        $this->_options = array_merge($defaults, $options);
        $this->open();
    }

    public function open() {
        $this->_ch = curl_init();

        //curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->_options ['user_agent']);
        curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('Expect:')); // for lighttpd 417 Expectation Failed
        curl_setopt($this->_ch, CURLOPT_TIMEOUT_MS,$this->_options['timeout_ms']);
        $this->_header = '';
        $this->_body = '';

        return $this;
    }

    public static function getCookieFullpath($file) {
        return sys_get_temp_dir() . $file . 'Cookie';
    }

    public function close() {
        if (is_resource($this->_ch)) {
            curl_close($this->_ch);
        }

//        if (isset($this->_cookie) && $this->_is_temp_cookie && is_file($this->_cookie)) {
//            unlink($this->_cookie);
//        }
    }

    public function cookie($file) {
        if (!isset($this->_cookie)) {
            if (!empty($this->_cookie) && $this->_is_temp_cookie && is_file($this->_cookie)) {
                unlink($this->_cookie);
            }

            $this->_cookie = tempnam($this->_options ['temp_root'], 'curl_manager_cookie_' . $file);
            $this->_is_temp_cookie = true;
        }

        curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->_cookie);
        curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $this->_cookie);

        return $this->_cookie;
    }

    public function getCookie($url = NULL) {
        if (empty($this->_cookie)) {
            $this->_cookie = tempnam($this->_options ['temp_root'], 'curl_manager_cookie_');
        }
        curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->_cookie);
        return $this->get($url);
    }

    public function ssl() {
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
        return $this;
    }

    public function proxy($host = null, $port = null, $type = null, $user = null, $pass = null, $auth = null) {
        $this->_proxy = isset($host) ? $host : $this->_proxy;
        $this->_proxy_port = isset($port) ? $port : $this->_proxy_port;
        $this->_proxy_type = isset($type) ? $type : $this->_proxy_type;
        $this->_proxy_auth = isset($auth) ? $auth : $this->_proxy_auth;
        $this->_proxy_user = isset($user) ? $user : $this->_proxy_user;
        $this->_proxy_pass = isset($pass) ? $pass : $this->_proxy_pass;

        if (!empty($this->_proxy)) {
            curl_setopt($this->_ch, CURLOPT_PROXYTYPE, $this->_proxy_type == 'HTTP' ? CURLPROXY_HTTP : CURLPROXY_SOCKS5 );
            curl_setopt($this->_ch, CURLOPT_PROXY, $this->_proxy);
            curl_setopt($this->_ch, CURLOPT_PROXYPORT, $this->_proxy_port);
        }

        if (!empty($this->_proxy_user)) {
            curl_setopt($this->_ch, CURLOPT_PROXYAUTH, $this->_proxy_auth == 'BASIC' ? CURLAUTH_BASIC : CURLAUTH_NTLM );
            curl_setopt($this->_ch, CURLOPT_PROXYUSERPWD, "[{$this->_proxy_user}]:[{$this->_proxy_pass}]");
        }

        return $this;
    }

    public function setCommon($header = array(), $timeout = 30, $referer = '') {
        curl_setopt($this->_ch, CURLOPT_REFERER, $referer);
        if (!empty($header)) {
            curl_setopt($this->_ch, CURLOPT_HTTPHEADER, (array) $header);
        }
        curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    }

    public function request($method, $url, $query = array(), $header = array(), $timeout = 30, $referer = '') {
        return $this->$method($url, $query, $header, $timeout, $referer);
    }

    public function post($url, $query = array(), $header = array(), $timeout = 30, $referer = '') {
        $this->setCommon($header, $timeout, $referer);
        if (is_array($query)) {
            $query = http_build_query($query);
        }
        curl_setopt($this->_ch, CURLOPT_URL, $url);
        curl_setopt($this->_ch, CURLOPT_POST, true);
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $query);
        $this->_requrest();
        return $this->_body;
    }

    public function get($url, $query = array(), $header = '', $timeout = 30, $referer = '') {
        $this->setCommon($header, $timeout, $referer);
        if (!empty($query)) {
            $url .= strpos($url, '?') === false ? '?' : '&';
            $url .= is_array($query) ? http_build_query($query) : $query;
        }
        curl_setopt($this->_ch, CURLOPT_URL, $url);
        $this->_requrest();
        return $this->_body;
    }

    public function put($url, $query = array(), $header = '', $timeout = 30, $referer = '') {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        return $this->post($url, $query, $header, $timeout, $referer);
    }

    public function delete($url, $query = array(), $header = '', $timeout = 30, $referer = '') {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->post($url, $query, $header, $timeout, $referer);
    }

    public function head($url, $query = array(), $header = '', $timeout = 30, $referer = '') {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
        return $this->post($url, $query, $header, $timeout, $referer);
    }

    public function options($url, $query = array(), $header = '', $timeout = 30, $referer = '') {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
        return $this->post($url, $query, $header, $timeout, $referer);
    }

    public function trace($url, $query = array(), $header = '', $timeout = 30, $referer = '') {
        curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, 'TRACE');
        return $this->post($url, $query, $header, $timeout, $referer);
    }

    public function connect() {

    }

    public function follow_location() {
        preg_match('#Location:\s*(.+)#i', $this->header(), $match);

        if (isset($match [1])) {
            $this->set_action('auto_location_gateway', $match [1], $this->effective_url());

            $this->get('auto_location_gateway')->follow_location();
        }

        return $this;
    }

    public function header() {
        return $this->_header;
    }

    public function body() {
        return $this->_body;
    }

    public function effective_url() {
        return curl_getinfo($this->_ch, CURLINFO_EFFECTIVE_URL);
    }

    public function http_code() {
        return curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);
    }

    private function _requrest() {
        $response = curl_exec($this->_ch);

        $errno = curl_errno($this->_ch);

        $header_size = curl_getinfo($this->_ch, CURLINFO_HEADER_SIZE);
        if ($errno > 0) {

            $result = array(
                'code' => curl_getinfo($this->_ch, CURLINFO_HTTP_CODE),
                'status' => '',
                'body' => [
                    'header' => substr($response, 0, $header_size),
                    'body' => substr($response, $header_size),
                    'http_code' => curl_getinfo($this->_ch, CURLINFO_HTTP_CODE),
                    'last_url' => curl_getinfo($this->_ch, CURLINFO_EFFECTIVE_URL),
                ]
            );
            throw (new PumpkinJobException("curl:" . curl_getinfo($this->_ch, CURLINFO_EFFECTIVE_URL) . " failed [$errno]"))->setResult($result);
        }




        $this->_header = substr($response, 0, $header_size);
        $this->_body = substr($response, $header_size);
        return $response;
    }

    public function __destruct() {
        $this->close();
    }
}