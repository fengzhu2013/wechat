<?php
namespace app\wechat\logic;


use think\Loader;
use think\Model;

class Media
{


    public function __construct()
    {
    }

    //获得素材列表
    public function  getMediaList($param)
    {
        //验证param
        $validate = Loader::validate('Media');
        if (!$validate->check($param,$validate->getRule('listRule'))) {
            return '50002';
        }

        //从微信服务器中拉取数据
        $Media = new \app\wechat\service\Media();
        @$data  = $Media->getMediaList($param['type'],$param['offset'],$param['count']);
        if (empty($data)) {
            return '50006';     //提示没有记录信息
        }
        return $data;
    }


    //获得素材
    public function getMedia($param)
    {
        //验证$param
        $validate = Loader::validate('Media');
        if (!$validate->check($param)) {
            return '50002';
        }

        //从微信中获取素材
        $Media = new \app\wechat\service\Media();
        @$data = $Media->getMedia($param['mediaId']);

        if (empty($data)) {
            return '50006';
        }
        return $data;
    }


}