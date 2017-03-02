<?php


namespace Wqy\WechatQy;

use InvalidArgumentException;

class JsonChecker
{
    use DiTrait;

    public function __invoke($json)
    {
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
    }
}
