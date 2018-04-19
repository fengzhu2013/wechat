<?php
namespace app\wechat\logic;

use app\wechat\model\User;

class Scan
{
    protected $msg;

    protected $userId;

    protected $timestamp;

    protected $sceneId;

    protected $event;

    public function __construct($msg)
    {
        $this->msg = $msg;
        $this->timestamp = $msg['CreateTime'];
    }

    //处理扫码关注
    public function handleScanSubscribe()
    {
        $Subscribe = new Subscribe($this->msg);
        $Subscribe->handleSubscribe();
        $this->userId = $Subscribe->getLogInfo();
    }

    protected function handleUserId()
    {
        if (empty($this->userId)) {
            //获得userId
            $openid = $this->msg['FromUserName'];
            $User   = new User();
            $info   = $User->getInfoByOpenid($openid);
            if ($info) {
                $this->userId = $info['userId'];
            }
        }
    }

    protected function handleOtherInfo()
    {
        $eventKey = $this->msg['EventKey'];
        //未关注
        if (strpos($eventKey,'qrscene_') !== false) {
            $this->sceneId = intval(str_replace('qrscene_','',$eventKey));
            $this->event   = '1';
        } else {
            $this->sceneId = $eventKey;
            $this->event   = '2';
        }
    }




    public function getLogMsg()
    {
        $this->handleUserId();
        $this->handleOtherInfo();
        return [
            'sceneId'       => $this->sceneId,
            'createTime'    => $this->timestamp,
            'userId'        => $this->userId,
            'event'         => $this->event,
            'scanTime'      => date('Y-m-d H:i:s',$this->timestamp),
        ];
    }






}