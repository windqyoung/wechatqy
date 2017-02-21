<?php


namespace Wqy\WechatQy;


class Url
{
    use DiTrait;

    /**
     * oauth2 授权地址, 由微信浏览器访问, 然后微信浏览器会跳转到redirect_uri?code=CODE&state=STATE
     */
    public function oauth2AuthorizeUrl($redirect, $state = 'STATE-1')
    {
        $params = [
            'appid' => $this->di['corpid'],
            'redirect_uri' => $redirect,
            'response_type' => 'code',
            'scope' => 'snsapi_base',
            'state' => $state,
        ];

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';

        return $url;
    }

    /**
     * 这个就是显示在oa的登录页上的微信登录<a href的值,
     * 必须由用户手动点这个a, 把http_referer给带到微信端
     * 用户授权后, 跳转到redirect_url?auth_code=xxx&expires_in=600&state=xxxx
     */
    public function loginpageUrl($redirect, $usertype = 'member', $state = 'STATE-2')
    {
        $params = [
            'corp_id' => $this->di['corpid'],
            'redirect_uri' => $redirect,
            'state' => $state,
            /**
             * redirect_uri支持登录的类型，有member(成员登录)、
             * admin(管理员登录)、all(成员或管理员皆可登录)，
             * 默认值为admin
             */
            'usertype' => $usertype,
        ];
        $url = 'https://qy.weixin.qq.com/cgi-bin/loginpage?' . http_build_query($params);

        return $url;
    }

    /**
     * 使用配置的uri当做跳转回来的uri
     * @param string $state
     * @return string
     */
    public function loginpageUrlWithDefaultCallbackUri($state = 'STATE-3')
    {
        $uri =  isset($this->di['redirectcallbackuri']) ? $this->di['redirectcallbackuri'] : $_SERVER['REQUEST_URI'];
        if (strpos($uri, 'http://') === 0 || strpos($uri, 'https://')) {
            // 绝对uri, 就用这个
        }
        else {
            $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
            $server = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] == '80' ? '' : ':' . $_SERVER['SERVER_PORT']);
            $uri = $http . '://' . $server . '/' . ltrim($uri, '/');
        }

        return $this->loginpageUrl($uri, 'member', $state);
    }
}
