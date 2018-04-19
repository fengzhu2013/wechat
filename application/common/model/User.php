<?php
namespace app\common\model;

use think\Model;

class User extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 获得最后一个userId
     * @return mixed|string
     */
    public function getLastUserId()
    {
        $info = self::where([])->field('user_id')->order('id','desc')->find();
        if (empty($info) || (isset($info->user_id) && empty($info->user_id))) {
            return 'tf18000100';
        }
        return $info->user_id;
    }






}