<?php


namespace Wqy\WechatQy;

use Pimple\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\HtmlFormatter;
use Exception;



class Handler
{
    use DiTrait;

    private $config;
    private $data;
    private $session;

    public function __construct($config, $data, & $session)
    {
        $this->config = $config;
        $this->data = $data;
        $this->session = & $session;
    }


    public function handle()
    {
        $di = new Container($this->config);
        $di->register(new DefaultServices());
        $this->setDi($di);

        // {{{
        if (isset($di['debug'])
                && $di['debug']
                && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false
                && ! isset($this->data['json'])) {
            $di['logger']->pushHandler($h = new StreamHandler('php://output'));
            $h->setFormatter(new HtmlFormatter());
        }
        // }}}

        try {
            $this->doHandle()->send();
        }
        catch (Exception $e) {
            $di['logger']->error('发生异常', ['data' => $this->data, 'e' => $e]);
            throw $e;
        }
    }

    protected function doHandle()
    {
        $data = $this->data;

        $this->di['logger']->info('请求数据', ['data' => $data]);

        $stateKey = md5(__CLASS__) . '_wechat_state';

        if (empty($data['state'])) {
            $sessionState = $this->session[$stateKey] = uniqid('st_');
            // 没state, 跳转到微信
            $url = $this->di['url']->loginpageUrlWithDefaultCallbackUri($sessionState);

            $this->di['logger']->info('请求URL', [
                'session_state' => $sessionState,
                'url' => $url,
            ]);

            if ($this->isJsonRequest()) {
                return $this->returnObject(['url' => $url]);
            }

            return $this->returnObject(sprintf('<a href="%s">使用微信登录</a>', $url));
        }
        else if (! empty($data['state']) && ! empty($data['auth_code'])) {
            $sessionState = isset($this->session[$stateKey]) ? $this->session[$stateKey] : null;

            $this->di['logger']->info('准备登录', ['session_state' => $sessionState]);

            if (empty($sessionState) || $sessionState != $data['state']) {
                throw new Exception('state丢失');
            }
            // 都有, 用户授权成功, 执行本网站登录
            return $this->login();
        }
        else {
            $this->di['logger']->error('发生错误', ['data' => $data]);
        }
    }

    protected function isJsonRequest()
    {
        return strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
                || isset($this->data['json']);
    }


    protected function login()
    {
        $wechatData = $this->getQyWechatData($this->data);
        $this->doLogin($wechatData);

        $location = $this->getLoginSuccessLocation();
        return $this->returnObject('登录成功', ['location' => $location]);
    }

    protected function getQyWechatData($data)
    {
        $ui = $this->di['userinfo'];

        $authCode = $data['auth_code'];
        $authInfo = $ui->byAuthCode($authCode);

        if (empty($authInfo['user_info']['userid'])) {
            throw new Exception('无法登录, 未获取到userid');
        }

        $userId = $authInfo['user_info']['userid'];
        $userinfo = $ui->byId($userId);

        return $userinfo;
    }

    protected function doLogin($wechatData)
    {
        // 使用微信用户信息进行登录 {{{

        // }}}
    }

    protected function getLoginSuccessLocation()
    {
        return '/';
    }

    protected function returnObject($content = null, $headers = [])
    {
        return new Response($content, $headers);
    }


}
