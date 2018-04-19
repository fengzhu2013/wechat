<?php
namespace app\menu\validate;

use app\common\logic\ArrayTool;
use think\Validate;

class Menu extends Validate
{

    protected $menu = ['click','view'];

    protected $rule = [
        'menuName'      => 'require|max:10',
        'menuLocation'  => 'require',
    ];

    protected $aClickRule = [
        'menuName'      => 'require|max:10',
        'menuType'      => 'require|acceptedSelf',
        'menuKey'       => 'require',
        'menuValue'     => 'require',
        'menuLocation'  => 'require'
    ];

    protected $aViewRule = [
        'menuName'      => 'require|max:10',
        'menuType'      => 'require|acceptedSelf',
        'menuKey'       => 'require',
        'menuLocation'  => 'require'
    ];

    protected $menuLocation = [];

    protected $menuLocationRule = [
        'abs'           => 'require|number|between:1,6',
        'ord'           => 'require|number|between:1,6',
    ];

    protected $imageRule = [
        'mediaId'       => 'require',
    ];

    protected $videoRule = [
        'title'         => 'require',
        'mediaId'       => 'require',
        'description'   => 'require',
        'thumbMediaId'  => 'require',
    ];

    /**
     * 验证添加菜单参数
     * @param $params
     * @return bool
     */
    public function checkAddMenu($params):bool
    {
        @$param = $params['info'];
        @$count = count($param);
        //菜单数目
        if (!$this->verifyLen($param,0,19)) {
            return false;
        }
        //验证每个菜单
        foreach ($param as $menu) {
            //必须存在menuLocation
            if (!$this->check($menu,$this->rule)) {
                return false;
            }
            //检验menuLocation是否符合要求
            $res = $this->check($menu['menuLocation'],$this->menuLocationRule);
            if (!$res) {
                return false;
            }
            //为了后期查位置是否重复
            $this->menuLocation[] = $menu['menuLocation'];

            //根据元素个数分别检验
            $menuCount = count($menu);
            switch ($menuCount) {
                case 2:             //父菜单，且没有子菜单
                    $ret = $this->check($menu,$this->rule);
                    if (!$ret) {
                        return false;
                    }
                    //纵坐标必须为1
                    if ($menu['menuLocation']['ord'] != 1) {
                        $this->error = '父菜单的纵坐标必须为1';
                        return false;
                    }
                    break;
                case 4:             //点击跳转菜单
                    if ($menu['menuType'] !== 'view') {
                        $this->error = 'view菜单定义有误';
                        return false;
                    }
                    $ret = $this->check($menu,$this->aViewRule);
                    if (!$ret) {
                        return false;
                    }
                    break;
                case 5:             //点击事件菜单
                    if ($menu['menuType'] !== 'click') {
                        $this->error = 'click菜单定义有误';
                        return false;
                    }
                    $ret = $this->check($menu,$this->aClickRule);
                    if (!$ret) {
                        return false;
                    }
                    break;
                default :
                    return false;
            }
        }

        //验证位置是否重复
        $menuLocationCount = count(ArrayTool::arrayUnique($this->menuLocation));
        if ($count !== $menuLocationCount) {
            $this->error = '菜单位置有重复，请查询';
            return false;
        }
        return true;

    }



}