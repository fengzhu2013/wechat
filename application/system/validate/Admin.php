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

    protected $login = [
        'userId'    => 'require|length:10',
        'password'  => 'require|min:6',
    ];

    protected $pageInfo = [
        'page'      => 'require|integer|gt:0',
        'pageSize'  => 'require|integer|gt:0',
    ];

    /**
     * 检验登陆参数
     * @param array $param
     * @return bool
     */
    public function checkLogin(array $param):bool
    {
        if (!$this->verifyLen($param,2)) {
            return false;
        }
        return $this->check($param,$this->login);
    }

    /**
     * 验证修改管理员信息参数
     * @param array $param
     * @return bool
     */
    public function checkModifyAdminInfo(array $param):bool
    {
        //验证传入的参数
        if (!$this->verifyLen($param,0,5)) {
            return false;
        }
        return $this->check($param,$this->modifyRule);
    }

    /**
     * 验证添加管理员信息参数
     * @param array $param
     * @return bool
     */
    public function checkAddAdminUser(array $param):bool
    {
        if (!$this->verifyLen($param,3)) {
            return false;
        }
        return $this->check($param);
    }

    /**
     * 验证获得管理员列表参数信息
     * @param array $param
     * @return bool
     */
    public function checkGetAdminList(array $param):bool
    {
        if (!$this->verifyLen($param,-1,3)) {
            return false;
        }
        return $this->check($param,$this->pageInfo);
    }

}