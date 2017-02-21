<?php


namespace Wqy\WechatQy;

use Monolog\Logger;

class Http
{
    use DiTrait;

    public function httpGetJson($url)
    {
        $rs = $this->httpGet($url);
        $json = json_decode($rs, true);

        if (isset($this->di['checkjson'])) {
            $this->di['checkjson']($json);
        }

        return $json;
    }

    public function httpGet($url)
    {
        $uni = uniqid('get_');

        if (isset($this->di['logger'])) {
            $this->di['logger']->info('http get', ['uni' => $uni, 'url' => $url]);
        }

        $rs = file_get_contents($url);

        if (isset($this->di['logger'])) {
            $this->di['logger']->info('http get rs', ['uni' => $uni, 'rs' => $rs]);
        }

        return $rs;
    }

    public function httpPostJson($url, $content, $header = null)
    {
        $rs = $this->httpPost($url, $content, $header);
        $json = json_decode($rs, true);

        if (isset($this->di['checkjson'])) {
            $this->di['checkjson']($json);
        }

        return $json;
    }

    public function httpPost($url, $content, $header = null)
    {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $header ?: 'Content-Type: application/x-www-form-urlencoded',
                'content' => is_array($content) ? http_build_query($content) : $content,
            ],
        ]);

        $uni = uniqid('post_');

        if (isset($this->di['logger'])) {
            $this->di['logger']->info('http post', ['uni' => $uni, 'url' => $url, 'content' => $content]);
        }

        $rs = file_get_contents($url, false, $ctx);

        if (isset($this->di['logger'])) {
            $this->di['logger']->info('http post rs', ['uni' => $uni, 'rs' => $rs]);
        }

        return $rs;
    }
}
