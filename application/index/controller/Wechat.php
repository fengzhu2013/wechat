<?php
namespace app\index\controller;

use EasyWeChat\Factory;

class Wechat
{
    protected $app_id;

    protected $secret;

    protected $token;

    protected $aes_key;

    protected $app;

    public function __construct()
    {
        //获得配置信息
        $config = $this->initSystem();
        if (empty($config)) {
            //配置信息
            $config = [
                'app_id'  => 'wxdda6ad81f6f31882',
                'secret'  => 'ee8a2d75446a00aaa5c750e4175f06d0',
                'token'   => 'trueFly',
                'aes_key' => 'I5nj4E8jUmjSsQRwaGnTx55LhwXelWWf8rOahKnuu5r',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file' => __DIR__ . '/wechat.log',
                ],
            ];
        }
        $this->app = Factory::officialAccount($config);
    }

    /**
     * connect server of wechat
     * only for wechat server
     */
    public function connectWeChat()
    {
        //消息处理
        $this->app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return '收到事件消息';
                    break;
                case 'text':
                    $msg = $this->test('hello');
                    return $msg;
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
        $response = $this->app->server->serve();

        // 将响应结果输出
        $response->send();
    }


    public function getMenuList()
    {
        $list = $this->app->menu->list();
        var_dump($list);
        exit;
    }

    /**
     * 初始化配置信息
     */
    private function initSystem()
    {
        //获得数据库的信息
        $info = [];
        return $info;
    }

    public function test($msg)
    {
        return 'test'.$msg;
    }



}