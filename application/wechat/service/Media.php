<?php
namespace app\wechat\service;

use app\wechat\controller\Index;

class Media
{
    protected $app;

    public function __construct()
    {
        $Index = new Index();
        $this->app = $Index->getApp();
    }

    /**
     * 获得素材列表
     * @param $type
     * @param int $offset
     * @param int $count
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function getMediaList($type,$offset = 0,$count = 20)
    {
        return $this->app->material->list($type, $offset, $count);
    }

    /**
     * 获取永久素材
     * @param $mediaId
     * @return mixed
     */
    public function getMedia($mediaId)
    {
        return $this->app->material->get($mediaId);
    }



}