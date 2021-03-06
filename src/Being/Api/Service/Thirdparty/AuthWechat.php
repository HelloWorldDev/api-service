<?php

namespace Being\Api\Service\Thirdparty;

use Being\Api\Service\HttpClient;
use Being\Api\Service\Sender;
use Being\Services\App\AppService;

class AuthWechat extends Auth
{
    private $httpClient;
    private $appId;
    private $secret;

    public function __construct(Sender $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function setConfig($config)
    {
        $this->appId = $config['wechat']['app_id'];
        $this->secret = $config['wechat']['secret'];
        return $this;
    }

    public function login($unionid, $code)
    {
        $wechatData = $this->fetchUserInfo($code);
        AppService::debug('wechat response:' . json_encode($wechatData), __FILE__, __LINE__);
        $wechatData = json_decode($wechatData, true);
        if (!isset($wechatData['openid'])) {
            return null;
        }
        //同一个公司下用户的unionid相同, 同一个公司下不同应用的openid不同
        $unionid = $wechatData['openid'];
        $avatar = isset($wechatData['headimgurl']) ? $wechatData['headimgurl'] : '';
        $nickname = isset($wechatData['nickname']) ? $wechatData['nickname'] : '';
        $realUnionid = isset($wechatData['unionid']) ? $wechatData['unionid'] : '';

        return [
            'unionid' => $unionid,
            'real_unionid' => $realUnionid,
            'openid' => $wechatData['openid'],
            'code' => $code,
            'avatar' => $avatar,
            'nickname' => $nickname,
        ];
    }

    private function fetchAccessTokenData($code)
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $query = [
            'appid' => $this->appId,
            'secret' => $this->secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $req = HttpClient::getRequest(HttpClient::GET, $url, $query, [], null);
        list($code, $body, $header) = $this->httpClient->send($req);
        $data = json_decode($body, true);
        if (isset($data['access_token'])) {
            return $data;
        }
        return null;
    }

    private function fetchUserInfo($code)
    {
        $accessTokenData = $this->fetchAccessTokenData($code);
        if (empty($accessTokenData)) {
            return null;
        }
        $url = 'https://api.weixin.qq.com/sns/userinfo';
        $query = [
            'access_token' => $accessTokenData['access_token'],
            'openid' => $accessTokenData['openid'],
            'lang' => 'zh_CN',
        ];

        $req = HttpClient::getRequest(HttpClient::GET, $url, $query, [], null);
        list($code, $body, $header) = $this->httpClient->send($req);
        return $body;
    }
}
