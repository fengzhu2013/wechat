<?php
namespace app\wechat\controller;

use app\common\service\WriteScanLog;
use app\common\service\WriteUserLog;
use app\wechat\logic\ResponseMsg;
use app\wechat\logic\Scan;
use app\wechat\logic\Subscribe;
use app\wechat\model\SystemInfo;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Text;

class Index
{
    protected $app_id;

    protected $secret;

    protected $token;

    protected $aes_key;

    protected $app;

    protected $timestamp;

    public function __construct()
    {
        $this->timestamp = time();
        //获得配置信息
        //配置信息
        $config     = [
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
        $systemInfo = $this->initSystem();
        if (!empty($systemInfo)) {
            $config['app_id'] = $systemInfo['appid'];
            $config['secret'] = $systemInfo['appsecret'];
            $config['token']  = $systemInfo['token'];
            if (!empty($systemInfo['aesKey'])) {
                $config['aes_key'] = $systemInfo['aesKey'];
            }
        }
        $this->app = Factory::officialAccount($config);
    }

    public function getApp()
    {
        return $this->app;
    }

    /**
     * connect server of wechat
     * only for wechat server
     */
    public function connectWeChat()
    {
        //消息处理
        $this->app->server->push(function ($message) {

            //消息排重
            if ($this->verifyMsgIsRepeat($message)) {
                return 'success';
            }
            //消息记录
            //消息处理
            @$this->timestamp = $message['CreateTime'];
            switch ($message['MsgType']) {
                case 'event':
                    return $this->handleEvent($message);
                    break;
                case 'text':
                    $msg = $this->handleText($message);
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

    //验证消息是否重复发送，重复返回true
    public function verifyMsgIsRepeat($msg)
    {
        return false;
    }

    //处理接收事件推送
    public function handleEvent($msg)
    {
        if (!isset($msg['EventKey'])) {
            //关注或取消关注
            switch ($msg['Event']) {
                case 'subscribe':
                    return $this->subscribe($msg);
                    break;
                case 'unsubscribe':
                    return $this->unSubscribe($msg);
                    break;
                case 'LOCATION':
                    return 'success';
                    break;
                default:
                    return 'success';
                    break;
            }
        } else {
            //扫码或自定义菜单
            //扫码
            if (isset($msg['Ticket'])) {
                switch ($msg['Event']) {
                    case 'SCAN':
                        return $this->scanOther($msg);
                        break;
                    case 'subscribe':
                        return $this->scanSubscribe($msg);
                        break;
                    default :
                        return new Text('success');
                        break;
                }
            } else {
                //自定义菜单
                switch ($msg['Event']) {
                    case 'VIEW':
                        return $this->menuView($msg);
                        break;
                    case 'CLICK':
                        return $this->menuClick($msg);
                        break;
                    default :
                        return new Text('success');
                        break;
                }
            }
        }
    }

    //处理收到的文字信息
    public function handleText($msg)
    {
        //处理逻辑关系

        //获得回复文字的信息（关键词）
        $ResponseMsg    = new ResponseMsg();
        $result         = $ResponseMsg->responseKeywords($msg['Content']);

        //记录日志
        $response       = $ResponseMsg->getResult();
        $userId         = \app\wechat\logic\Common::getUserIdByOpenId($msg['FromUserName']);
        WriteUserLog::writeUserLog($userId,WriteUserLog::TEXT,$response,$this->timestamp);

        //返回信息
        return $result;
    }


    //处理关注
    public function subscribe($msg)
    {
        //处理关注时逻辑
        $Subscribe      = new Subscribe($msg);
        $Subscribe->handleSubscribe();


        //获得关注时的回复消息
        $ResponseMsg    = new ResponseMsg();
        $result         = $ResponseMsg->responseSubscribe();

        //记录日志
        $userId         = $Subscribe->getLogInfo();
        $response       = $ResponseMsg->getResult();
        WriteUserLog::writeUserLog($userId,WriteUserLog::SUBSCRIBE,$response,$this->timestamp);

        //返回消息
        return $result;
    }

    //处理取消关注
    public function unSubscribe($msg)
    {
        //处理逻辑
        $Subscribe  = new Subscribe($msg);
        $Subscribe->handleUnSubscribe();

        //记录日志信息
        $userId     = $Subscribe->getLogInfo();
        $response   = 'success';
        WriteUserLog::writeUserLog($userId,WriteUserLog::UN_SUBSCRIBE,$response,$this->timestamp);

        //回复消息
        $result = new Text($response);
        return $result;
    }

    //处理扫码关注
    public function scanSubscribe($msg)
    {
        //处理扫码逻辑
        $Scan           = new Scan($msg);
        $Scan->handleScanSubscribe();

        //获得扫码记录信息并写入日志
        $msg            = $Scan->getLogMsg();
        WriteScanLog::writeScanLog($msg);

        //获得关注时回复信息
        $ResponseMsg    = new ResponseMsg();
        $result         = $ResponseMsg->responseSubscribe();
        return $result;
    }

    //处理扫码非关注事件
    public function scanOther($msg)
    {
        //记录扫码日志
        $Scan = new Scan($msg);
        $msg  = $Scan->getLogMsg();
        WriteScanLog::writeScanLog($msg);

        //返回信息
        return new Text("感谢关注与支持哦");
    }

    //处理自定义菜单跳转事件
    public function menuView($msg)
    {
        //没有逻辑处理

        //记录信息
        //获得回复信息
        $response   = ['url' => $msg['EventKey']];
        $userId     = \app\wechat\logic\Common::getUserIdByOpenId($msg['FromUserName']);
        WriteUserLog::writeUserLog($userId,WriteUserLog::MENU_VIEW,$response,$this->timestamp);

        return new Text('success');
    }

    //处理自定义菜单点击事件
    public function menuClick($msg)
    {
        //逻辑处理

        //获得回复信息
        $ResponseMsg    = new ResponseMsg();
        $result         = $ResponseMsg->responseMenuKey($msg['EventKey']);

        //记录日志信息
        $response       = $ResponseMsg->getResult();
        $userId         = \app\wechat\logic\Common::getUserIdByOpenId($msg['FromUserName']);
        WriteUserLog::writeUserLog($userId,WriteUserLog::MENU_CLICK,$response,$this->timestamp);

        //返回信息
        return $result;
    }






    /**
     * 初始化配置信息
     */
    private function initSystem()
    {
        //获得数据库的信息
        $SystemInfo = new SystemInfo();
        $info       = $SystemInfo->getInfoDesc();
        return $info;
    }




}