<?php
namespace app\wechat\validate;

use think\Validate;

class Media extends Validate
{
    protected $menu = [
        'image','video','voice','news'
    ];


    protected $listRule = [
        'type'      => 'require|acceptedSelf',
        'offset'    => 'number',
        'count'     => 'number|between:1,20',
    ];

    protected $rule = [
        'mediaId'  => 'require',
    ];




}