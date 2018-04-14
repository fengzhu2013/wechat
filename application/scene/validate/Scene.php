<?php
namespace app\scene\validate;

use think\Validate;

class Scene extends Validate
{
    protected $menu = ['1','2','3','4'];

    protected $modifyRule = [
        'id'        => 'require|number',
        'status'    => 'acceptedSelf'
    ];




}