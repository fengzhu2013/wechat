<?php
namespace app\system\validate;

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

    /**
     * 检验修改系统参数信息
     * @param array $param
     * @return bool
     */
    public function checkModifySystemInfo(array $param):bool
    {
        if (!$this->verifyLen($param,1,6)) {
            return false;
        }
        return $this->check($param,$this->modifyRule);
    }



}