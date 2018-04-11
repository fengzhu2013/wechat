<?php
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{

    protected $menu = ['0','1'];

    protected $rule = [
        'userId'    => 'require|length:10',
        'password'  => 'require|min:6',
        'adminName' => 'require|min:3|max:64',
    ];

    protected $modifyRule = [
        'userId'    => 'require|length:10',
        'password'  => 'min:6',
        'adminName' => 'min:3|max:64',
        'status'    => 'acceptedSelf'
    ];


}