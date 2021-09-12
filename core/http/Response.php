<?php
namespace Core\http;
class Response {

    /**
     * @var \Swoole\Http\Response
     */
    protected $swooleReponse;
    protected $body;

    /**
     * Response constructor.
     * @param $swooleReponse
     */
    public function __construct($swooleReponse) {
        $this->swooleReponse = $swooleReponse;
        $this->setHeader("Content-Type", "text/plain;charset=utf8");
    }

    public static function init(\Swoole\Http\Response $response) {
        return new self($response);
    }

    /**
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body) {
        $this->body = $body;
    }

    public function setHeader($key, $value) {
        $this->swooleReponse->header($key, $value);
    }

    public function writeHttpStatus($code = 200) {
        $this->swooleReponse->status($code);
    }

    public function writeHtml($html = "") {
        $this->swooleReponse->write($html);
    }

    public function redirect($url, $code=301) {
        $this->writeHttpStatus($code);
        $this->setHeader("Location", $url);
    }

    public function end() {
        // $this->swooleReponse->write($this->getBody());
        // $this->swooleReponse->end();

        $jsonConvert = ["array"];
        $ret = $this->getBody();
        if (in_array(gettype($ret), $jsonConvert)) {
            $this->swooleReponse->header("Content-type","application/json;charset=utf-8");
            $this->swooleReponse->write(json_encode($ret));
        } else {
            $this->swooleReponse->write($ret);
        }

        $this->swooleReponse->end();
    }
}