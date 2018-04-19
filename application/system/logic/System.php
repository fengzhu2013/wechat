<?php
namespace app\system\logic;


use app\common\logic\ArrayTool;
use app\common\logic\BaseLogic;
use app\system\model\Admin;
use app\system\User\User;
use think\Loader;
use app\system\model\SystemInfo;

class System extends BaseLogic
{
    protected $request;


    public function __construct($loginLogInfo = [])
    {
        parent::__construct($loginLogInfo,true);

    }

    //添加微信系统必要信息，如appid等
    public function addSystemInfo($param)
    {
        //是否超管
        if ($this->loginType != 2) {
            return '30005';     //提示权限不够
        }

        //插入一条数据
        $SystemInfo = new SystemInfo($param);
        if ($SystemInfo->allowField(true)->saveSelf()) {
            $this->initLog(['id' => intval($SystemInfo->id)]);
            return true;
        }

        //提示添加失败
        return '10002';
    }

    //修改系统必要信息
    public function modifySystemInfo($param)
    {
        //验证登陆权限
        if ($this->loginType != 2) {
            return '30005';
        }

        //修改信息
        $SystemInfo = SystemInfo::get($param['id']);
        if (!$SystemInfo) {
            return '40004';         //识别标识符错误或不存在
        }
        if (!$SystemInfo->checkUpdateInfo($param)) {
            return '50005';         //提示没有更新的信息
        }
        //更改
        if ($SystemInfo->saveSelf()) {
            $this->initLog(['id' => $param['id']]);
            return true;
        }

        //提示修改失败
        return '10003';

        /*if ($SystemInfo->allowField(true)->saveSelf($param,['id' => $param['id']])) {
            $this->initLog(['id' => $param['id']]);
            return true;
        }*/

    }

    //获得微信必要信息
    public function getSystemInfo()
    {
        $SystemInfo = new SystemInfo();
        $ret = $SystemInfo->getLastInfo();

        if ($ret) {
            return $ret;
        }

        //提示失败
        return '10001';
    }

    //获得用户信息
    public function getUserInfo($param)
    {
        if (empty($param)) {
            //获得登陆者信息
            return ArrayTool::removeKey($this->loginInfo,'password');
        }
        //验证登陆权限
        if ($this->loginType != 2) {
            return '30005';
        }

        if (!isset($param['userId']) || empty($param['userId'])) {
            return '50002';          //提示传参格式不符合要求
        }
        //获得其他人信息
        $AdminObj = new Admin();
        $ret = $AdminObj::getSelf(['userId' => $param['userId']]);

        if ($ret) {
            return ArrayTool::removeKey($ret,'password');
        }
        return '40004';     //提示标识符错误或不存在
    }

    //修改用户信息
    public function modifyUserInfo($param)
    {
        if ($this->loginType !=2 && $this->userId !== $param['userId']) {
            return '30005';     //提示权限不够
        }

        if (isset($param['password'])) {
            $param['password'] = md5($param['password']);
        }

        $Admin = Admin::getSelfObj(['userId' => $param['userId']]);
        if (!$Admin->checkUpdateInfo($param)) {
            return '50005';     //提示没有更新信息
        }
        //更新数据
        if ($Admin->saveSelf()) {
            $this->initLog(['id' => $Admin->id]);
            return true;
        }

        return '10003';
    }

    //添加一个管理员
    public function addAdminUser($param)
    {
        $validate = Loader::validate('Admin');
        if (!$validate->check($param)) {
            return '50002';         //传参不符合格式
        }

        if (count($param) != 3) {
            return '50003';         //传参不安全
        }

        //查看userId是否真实存在
        $User = new User();
        if (!$User->verifyUserId($param['userId'])) {
            return '40002';         //用户名错误或不存在
        }

        $Admin = new Admin($param);
        //验证是否已是管理员
        if ($Admin->verifyUserId($param['userId'])) {
            return '30006';         //提示已是管理员
        }

        //确定好插入的数据
        $Admin->perfectAddAdminInfo($this->timestamp);
        //开始插入数据
        if ($Admin->allowField(true)->saveSelf()) {
            $this->initLog(['id' => $Admin->id]);
            return true;
        }

        //提示添加失败
        return '10002';
    }

    //获得管理员列表
    public function getAdminList($param)
    {
        //验证权限
        if ($this->loginType != 2) {
            return '30005';
        }
        //获得数据
        $Admin = new Admin();
        $info = $Admin->getPage($param);
        if (!$info) {
            return '50006';         //提示没有记录信息
        }
        //去掉密码字段
        foreach ($info as &$val) {
            $val = ArrayTool::removeKey($val,'password');
        }
        //获得总记录数
        $count = $Admin->getCount();
        if (!$count) {
            return '20003';         //提示系统开小差了，请等等
        }

        //返回数据
        $ret = [
            'count' => $count,
            'data'  => $info,
        ];
        return $ret;
    }

    //系统登录
    public function login($info)
    {
        //通过userId获得相应的信息
        $AdminObj = new Admin();
        $userInfo = $AdminObj->getSelf(['userId' => $info->post('userId')]);
        if (!$userInfo) {
            return '40002';
        }

        //账号状态
        if (!$userInfo['status'] || $userInfo['status'] != 1) {
            return '30008';
        }

        //比对密码是否正确
        if (md5($info->post('password')) !== $userInfo['password']) {
            return '40003';         //账号或密码错误
        }

        //初始化记录日志的必要值
        $this->initLog($userInfo);

        //生成SESSION_ID
        $SESSION_ID = createSESSION_ID($userInfo['userId'],$userInfo['password'],$this->timestamp);
        return ['SESSION_ID' => $SESSION_ID];
    }


}