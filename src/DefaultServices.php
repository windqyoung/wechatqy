<?php

namespace Wqy\WechatQy;


use Pimple\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Doctrine\Common\Cache\PhpFileCache;
use InvalidArgumentException;

class DefaultServices implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     * @see \Pimple\ServiceProviderInterface::register()
     */
    public function register(\Pimple\Container $pimple)
    {
        $pimple['http'] = function ($di) {
            $http = new Http();
            $http->setDi($di);

            return $http;
        };

        $pimple['logger'] = function ($di) {
            $logger = new Logger('qy');
            $h = new StreamHandler($di['logfile']);
            $logger->pushHandler($h);

            return $logger;
        };

        $pimple['cache'] = function ($di) {
            $cache = new PhpFileCache($di['cachedir']);

            return $cache;
        };

        $pimple['checkjson'] = $pimple->protect(function ($json) {
            if (! is_array($json)) {
                throw new InvalidArgumentException('not array');
            }
            if (isset($json['errcode']) && $json['errcode'] != 0) {
                throw new InvalidArgumentException(sprintf(
                    'error: %s(%s)',
                    isset($json['errmsg']) ? $json['errmsg'] : 'not found errmsg',
                    $json['errcode']
                ));
            }
        });

        $pimple['accesstoken'] = function ($di) {
            $ac = new AccessToken();
            $ac->setDi($di);

            return $ac->get();
        };

        $pimple['url'] = function ($di) {
            $url = new Url();
            $url->setDi($di);

            return $url;
        };

        $pimple['userinfo'] = function ($di) {
            $ui = new UserInfo();
            $ui->setDi($di);

            return $ui;
        };
    }

}


