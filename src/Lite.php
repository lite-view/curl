<?php


namespace LiteView\Curl;


class Lite
{
    private $cURL;
    private $headers = [
        //"Content-Type: application/json",
        //"Content-Type: text/plain",
        //"Content-Type: text/html",
    ];
    private $info;

    private function __construct(array $headers)
    {
        $this->headers = $headers;
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true); //是否返回响应，false：会直接输出
        curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, false);
    }

    private function exec($url, $timeout)
    {
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_TIMEOUT, $timeout);
        $response = curl_exec($this->cURL);//响应信息
        $error = curl_error($this->cURL);//错误信息
        $this->info = curl_getinfo($this->cURL);//响应头，请求头等信息
        $errCode = curl_errno($this->cURL);
        curl_close($this->cURL);
        if ($error) {
            trigger_error("Lite exec failed, errorMsg:$error;errorCode:$errCode");
        }
        return $response;
    }

    public static function request($headers = [])
    {
        return new self($headers);
    }

    public function get($url, $timeout = 12, &$info = null)
    {
        $rsp = $this->exec($url, $timeout);
        $info = $this->info;
        return $rsp;
    }

    public function post($url, $payload, $timeout = 12, &$info = null)
    {
        curl_setopt($this->cURL, CURLOPT_POST, true);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $payload);
        $rsp = $this->exec($url, $timeout);
        $info = $this->info;
        return $rsp;
    }

    public function setHeader($header)
    {
        if (is_array($header)) {
            $this->headers = array_merge($this->headers, $header);
        }
        if (is_string($header)) {
            $this->headers[] = $header;
        }
        return $this;
    }

    public function setAgent($user_agent = 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)')
    {
        curl_setopt($this->cURL, CURLOPT_USERAGENT, $user_agent);
        return $this;
    }

    public function setProxy($host, $port, $username = null, $password = null)
    {
        curl_setopt($this->cURL, CURLOPT_PROXY, "$host");
        curl_setopt($this->cURL, CURLOPT_PROXYPORT, "$port");
        if ($username && $password) {
            curl_setopt($this->cURL, CURLOPT_PROXYUSERPWD, $username . ':' . $password); // 如果代理需要认证
        }
        return $this;
    }

    public function setSSL($cert, $key)
    {
        curl_setopt($this->cURL, CURLOPT_SSLKEYTYPE, 'PEM'); //默认格式为PEM，可以去掉
        curl_setopt($this->cURL, CURLOPT_SSLCERT, $cert); // 证书文件
        curl_setopt($this->cURL, CURLOPT_SSLKEY, $key); // 证书文件
        return $this;
    }

    public function needHeader()
    {
        curl_setopt($this->cURL, CURLINFO_HEADER_OUT, true); //返回请求头信息
        curl_setopt($this->cURL, CURLOPT_HEADER, 1); //返回响应头信息，会和body放在一起
        return $this;
    }

    public function followLocation()
    {
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, true);
        return $this;
    }
}
