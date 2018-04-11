<?php
namespace App\system\controller;

use EasyWeChat\Factory;
class Index
{

    protected $app_id;

    protected $secret;

    protected $token;

    protected $aes_key;

    public function __construct()
    {
        //获得配置信息
        $this->initSystem();
    }

    /**
     * connect server of wechat
     * only for wechat server
     */
    public function connectWeChat()
    {
        //配置信息
        $config = [
            'app_id'  => 'wx6974dcaaa1831a5d',
            'secret'  => '44f163398c5bffbdffa2750cf4bff070',
            'token'   => 'trueFly',
            'aes_key' => 'I5nj4E8jUmjSsQRwaGnTx55LhwXelWWf8rOahKnuu5r',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__ . '/wechat.log',
            ],
        ];

        // 使用配置来初始化一个公众号应用实例。
        $app = Factory::officialAccount($config);

        //消息处理
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return '收到事件消息';
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });

        //执行服务端业务
        $response = $app->server->serve();

        // 将响应结果输出
        $response->send();
    }

    /**
     * 初始化配置信息
     */
    private function initSystem()
    {
        //获得数据库的信息
        $info = [];
    }

    /**
     * 系统生成管理员
     */
    public function createSystemAdmin()
    {

    }


    public function systemLogin()
    {

    }






}