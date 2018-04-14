<?php
namespace app\system\validate;

use think\Validate;

class Admin extends Validate
{

    protected $menu = ['0','1'];

    //默认增加验证规则
    protected $rule = [
        'userId'    => 'require|length:10',
        'password'  => 'require|min:6',
        'adminName' => 'require|min:2|max:64',
    ];

    protected $modifyRule = [
        'userId'    => 'require|length:10',
        'password'  => 'min:6',
        'adminName' => 'min:2|max:64',
        'status'    => 'acceptedSelf'
    ];


}