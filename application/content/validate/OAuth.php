<?php
namespace app\content\validate;

use think\Validate;

class OAuth extends Validate
{
    protected $menu = ['snsapi_base','snsapi_userinfo'];

    protected $scope = [
        'articleId' => 'require|integer|gt:0'
    ];


    /**
     * 验证获得code参数
     * @param array $param
     * @return bool
     */
    public function checkView(array $param = []):bool
    {
        if (!$this->verifyLen($param,1)) {
            return false;
        }
        return $this->check($param,$this->scope);
    }
}