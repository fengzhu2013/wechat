<?php
namespace app\wechat\service;

use app\wechat\controller\Index;

class QrCode
{

    protected $app;

    public function __construct()
    {
        $wechat = new Index();
        $this->app = $wechat->getApp();
    }

    /**
     * 获得永久二维码
     * @param int $sceneId
     * @return array
     */
    public function getForever(int $sceneId):array
    {
        $res = $this->app->qrcode->forever($sceneId);
        $res['sceneId'] = $sceneId;
        if (isset($res['ticket']) && !empty($res['ticket'])) {
            $res['url'] = $this->getQrCode($res['ticket']);
            unset($res['ticket']);
        }
        return $res;
    }

    public function getQrCode($ticket)
    {
        return $this->app->qrcode->url($ticket);
    }




}