<?php
namespace app\scene\validate;

use think\Validate;

class SceneInfo extends Validate
{

    protected $menu = ['a','s'];

    protected $rule = [
        'nameCn'    => 'require|max:64|min:2',
        'sceneType' => 'require|acceptedSelf',
        'province'  => 'max:50',
        'phone'     => 'max:15',
        'addressCn' => 'max:50',
        'city'      => 'max:50',
        'area'      => 'max:50',
        'startDate' => 'date',
        'endDate'   => 'date',
    ];

    protected $modifyRule = [
        'id'        => 'require|number',
        'nameCn'    => 'max:64|min:2',
        'sceneType' => 'acceptedSelf',
        'province'  => 'max:50',
        'phone'     => 'max:15',
        'addressCn' => 'max:50',
        'city'      => 'max:50',
        'area'      => 'max:50',
        'startDate' => 'date',
        'endDate'   => 'date',
    ];

    /**
     * 检验修改渠道信息参数
     * @param array $param
     * @return bool
     */
    public function checkModifyScene(array $param):bool
    {
        if (!$this->verifyLen($param,-1,3)) {
            return false;
        }
        return $this->check($param,$this->modifyRule);
    }


}