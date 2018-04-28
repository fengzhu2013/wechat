<?php
namespace app\wechat\service;

use app\common\service\Status;
use app\wechat\controller\Index;
use EasyWeChat\Factory;

class Oauth
{
    protected $app;

    protected $oauth;

    protected $systemInfo;

    public function __construct($callback = null)
    {
        $Index = new Index();
        $this->systemInfo = $Index->getSystem();
        if (empty($this->systemInfo)) {
            Status::processMsg("没有系统信息");
        }
        $config      = $this->initConfig($this->systemInfo,$callback);
        $this->app   = Factory::officialAccount($config);
        $this->oauth = $this->app->oauth;
    }

    public function initConfig($systemInfo,$callback)
    {
        $config = [
            'app_id'        => $systemInfo['appid'],
            'secret'        => $systemInfo['appsecret'],
            'token'         => $systemInfo['token'],

            'oauth'         => [
                'scopes'   => ['snsapi_base'],
                'callback' => $callback,
            ],

            'response_type' => 'array',

            'log'           => [
                'level' => 'debug',
                'file' => __DIR__ . '/wechat.log',
            ],
        ];
        if (!empty($systemInfo['aesKey'])) {
            $config['aes_key'] = $systemInfo['aesKey'];
        }
        return $config;
    }


    public function getCode(array $param = [])
    {
        //验证数据
        @$scopes = !empty($param['scope'])?$param['scope']:'snsapi_base';
        $response = $this->oauth->scopes([$scopes])->redirect();
        return $response;
    }

    public function getUserInfo()
    {
        $user = $this->oauth->user();
        return $user->getOriginal();
    }


    public function getCodeUrl($param)
    {
        $appid  = $this->systemInfo['appid'];
        $redirectUrl = urlencode($param['url']);
        $scope  = $param['scope'];
        $url    = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirectUrl}&response_type=code&scope={$scope}&state=STATE#wechat_redirect";
        return ['url' => $url];
    }




}