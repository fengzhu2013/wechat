<?php
namespace app\menu\logic;

use app\common\logic\BaseLogic;
use function Couchbase\fastlzCompress;
use think\Lang;
use think\Loader;

class Menu extends BaseLogic
{
    public function __construct(array $loginLogInfo = [], $isVerifyType = false)
    {
        parent::__construct($loginLogInfo, $isVerifyType);
    }

    //创建菜单
    public function addMenu($param)
    {
        $info = $param['info'];

        //插入数据库
        $Menu       = new \app\menu\model\Menu();
        //格式化数据
        $insertInfo = $this->formatInsertInfo($info);
        $ret        = $Menu->insertAll($insertInfo,true);
        if (!$ret) {
            return false;
        }

        //创建菜单
        //构造数据结构
        $buttons    = $this->formatMenuInfo($info);
        $WechatMenu = new \app\wechat\service\Menu();
        $res        = $WechatMenu->createMenu($buttons);
        if (0 !== $res['errcode']) {
            return false;
        }
        return true;
    }

    /**
     * 格式化信息，为了生成微信菜单所需要的数据结构
     * @param $info
     * @return array
     */
    public function formatMenuInfo($info)
    {
        $buttons = [];
        $button1 = [];
        $button2 = [];
        $button3 = [];
        foreach ($info as $val) {
            $menuLocation = $val['menuLocation'];
            $button = 'button'.$menuLocation['abs'];
            //父菜单
            if ($menuLocation['ord'] == 1) {
                ${$button}['name'] = $val['menuName'];
                if (isset($val['menuType'])) {
                    ${$button}['type'] = $val['menuType'];
                    if ($val['menuType'] == 'click') {
                        ${$button}['key'] = $val['menuKey'];
                    }
                    if ($val['menuType'] == 'view') {
                        ${$button}['url'] = $val['menuKey'];
                    }
                }
                /*if ($menuLocation['abs'] == 1) {
                    $button1 = ['name' => $val['menuName']];
                    if (isset($val['menuType'])) {
                        $button1['type'] = $val['menuType'];
                        if ($val['menuType'] == 'click') {
                            $button1['key'] = $val['menuKey'];
                        }
                        if ($val['menuType'] == 'view') {
                            $button1['url'] = $val['menuKey'];
                        }
                    }
                }*/
            } else {
                //子菜单
                $key = intval($menuLocation['ord']) - 2;
                ${$button}['sub_button'][$key] = ['name' => $val['menuName'],'type' => $val['menuType']];
                if ($val['menuType'] == 'click') {
                    ${$button}['sub_button'][$key]['key'] = $val['menuKey'];
                }
                if ($val['menuType'] == 'view') {
                    ${$button}['sub_button'][$key]['url'] = $val['menuKey'];
                }
            }
        }
        if (!empty($button1)) {
            $buttons[] = $button1;
        }
        if (!empty($button2)) {
            $buttons[] = $button2;
        }
        if (!empty($button3)) {
            $buttons[] = $button3;
        }
        return $buttons;
    }


    /**
     * 格式化信息，为了插入数据库
     * @param $info
     * @return mixed
     */
    public function formatInsertInfo($info)
    {
        foreach ($info as &$val) {
            $val['menuLocation'] = json_encode($val['menuLocation']);
            if (isset($val['menuValue']) && is_array($val['menuValue'])) {
                $val['menuValue'] = json_encode($val['menuValue']);
            }
        }
        return $info;
    }


    //获得菜单
    public function getMenu()
    {
        //从数据库中获取数据
        $Menu = new \app\menu\model\Menu();
        $info = $Menu->getAll();
        if (empty($info)) {
            return '50006';     //提示没有信息
        }
        //处理数据
        foreach ($info as &$val) {
            $val['menuLocation'] = json_decode($val['menuLocation'],true);
            if (isset($val['menuValue']) && !empty($val['menuValue'])) {
                @$content = json_decode($val['menuValue'],true);
                if ($content) {
                    $val['menuValue'] = $content;
                }
            }
        }
        //返回数据
        return $info;
    }

    /**
     * 删除所有菜单
     * @return bool|string
     */
    public function deleteAllMenu()
    {
        //删除微信上的菜单
        $Menu   = new \app\wechat\service\Menu();
        $ret    = $Menu->deleteAllMenu();
        if ($ret['errcode'] !== 0) {
            return false;
        }

        //删除本地数据库
        $Menu2  = new \app\menu\model\Menu();
        $where['id'] = ['>',0];
        if (!$Menu2->deleteInfo($where)) {
            return '20001';     //提示系统错误
        }
        return true;
    }





}