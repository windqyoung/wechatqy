<?php


namespace Wqy\WechatQy;


class Response
{
    private $content;

    private $headers;

    public function __construct($content = null, $headers = [])
    {
        if (is_array($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
            $headers['content-type'] = 'application/json';
        }
        $this->content = $content;
        $this->headers = $headers;
    }

    public function send()
    {
        if ($this->headers) {
            foreach ($this->headers as $k => $v) {
                header("$k: $v");
            }
        }

        echo $this->content;
    }
}
