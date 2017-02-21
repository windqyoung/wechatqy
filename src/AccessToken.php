<?php


namespace Wqy\WechatQy;


class AccessToken
{
    use DiTrait;

    public function getJson()
    {
        $cacheId = 'accesstoken.' . $this->di['corpid'] . '.cache';

        if (isset($this->di['cache'])) {
            $c = $this->di['cache']->fetch($cacheId);
            if ($c) {
                if (isset($this->di['logger'])) {
                    $this->di['logger']->info('get access token from cache', ['id' => $cacheId]);
                }
                return $c;
            }
        }

        $params = [
            'corpid' => $this->di['corpid'],
            'corpsecret' => $this->di['corpsecret'],
        ];

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?' . http_build_query($params);

        $json = $this->di['http']->httpGetJson($url);

        if (isset($this->di['cache'])) {
            $saved = $this->di['cache']->save($cacheId, $json, $json['expires_in'] - 1000);
            if (isset($this->di['logger'])) {
                $this->di['logger']->info('save access token to cache', ['id' => $cacheId, 'saved' => $saved]);
            }
        }

        return $json;
    }

    public function get()
    {
        return $this->getJson()['access_token'];
    }
}
