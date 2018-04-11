<?php
namespace app\admin\validate;

use think\Validate;

class SystemInfo extends Validate
{

    protected $rule = [
        'appid'         => 'require',
        'appsecret'     => 'require',
        'token'         => 'require|max:32|min:3',
        'aesKey'        => 'length:43',
    ];

    protected $modifyRule = [
        'id'            => 'require',
        'token'         => 'max:32|min:3',
        'aesKey'        => 'length:43',
    ];

    public function getRule($name)
    {
        return $this->{$name};
    }



}