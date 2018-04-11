<?php
namespace app\common\validate;

use think\Validate;

class Common extends Validate
{

    protected $pageRule = [
        'page'      => 'number|egt:1',
        'pageSize'  => 'number',
    ];
}