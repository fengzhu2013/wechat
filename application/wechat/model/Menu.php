<?php
namespace app\wechat\model;

use think\Model;

class Menu extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 通过key获得菜单的value
     * @param $key
     * @return mixed
     */
    public function getValueByKey($key)
    {
        $where = ['menu_key' => $key,'menu_type' => 'click'];
        $info  = self::get($where);
        return $info->getAttr('menu_value');
    }



}