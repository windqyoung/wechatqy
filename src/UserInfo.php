<?php


namespace Wqy\WechatQy;


class UserInfo
{
    use DiTrait;

    /**
     * {"UserId":"useridxxx","DeviceId":"1234abcd"}
     */
    public function byCode($code)
    {
        $params = [
            'access_token' => $this->di['accesstoken'],
            'code' => $code,
        ];

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?' . http_build_query($params);

        return $this->di['http']->httpGetJson($url);
    }

    /**
     * @param string $id 企业号上的通讯录中显示的账号
     *
     * {
     *    "errcode": 0,
     *    "errmsg": "ok",
     *    "userid": "zhangsan",
     *    "name": "李四",
     *    "department": [1, 2],
     *    "position": "后台工程师",
     *    "mobile": "15913215421",
     *    "gender": "1",
     *    "email": "zhangsan@gzdev.com",
     *    "weixinid": "lisifordev",
     *    "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0",
     *    "status": 1,
     *    "extattr": {"attrs":[{"name":"爱好","value":"旅游"},{"name":"卡号","value":"1234567234"}]}
     * }
     */
    public function byId($id)
    {
        $params = [
            'access_token' => $this->di['accesstoken'],
            'userid' => $id,
        ];
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?' . http_build_query($params);

        return $this->di['http']->httpGetJson($url);
    }


    public function toOpenId($userId, $agentId = null)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=' . $this->di['accesstoken'];

        $data = [ 'userid' => $userId, 'agentid' => $agentId, ];

        $json = $this->di['http']->httpPostJson($url, json_encode($data));

        return isset($json['openid']) ? $json['openid'] : null;
    }

    public function toUserId($openId)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=' . $this->di['accesstoken'];

        $data = [ 'openid' => $openId, ];

        $json = $this->di['http']->httpPostJson($url, json_encode($data));

        return isset($json['userid']) ? $json['userid'] : null;
    }


    /**
     * 根据返回值里面的userid, 调用 self::byId() 能取到用户信息
     * userid只有usertype = 2, 5 有, 位置为 $ret['user_info']['userid']
     * 按道理说, 可以直接用isset来判断
     *
     * @note 创建者或外部系统管理员没有返回userid这个字段, 所以无法登录.
     *
     * @return array
     *
     * usertype: 登录用户的类型：1.企业号创建者 2.企业号内部系统管理员 3.企业号外部系统管理员 4.企业号分级管理员 5. 企业号成员
     */
    public function byAuthCode($authCode)
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info?access_token=' . $this->di['accesstoken'];

        $data = [ 'auth_code' => $authCode, ];

        return $this->di['http']->httpPostJson($url, json_encode($data));
    }
}
