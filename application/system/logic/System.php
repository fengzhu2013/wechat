<?php
namespace app\system\logic;


use app\common\logic\ArrayTool;
use app\system\model\Admin;
use app\system\User\User;
use think\Loader;
use app\system\model\SystemInfo;

class System
{
    protected $request;

    protected $userId;

    protected $objectId;

    /**
     * 登陆身份类型,1-普通，2-超管
     * @var
     */
    private $loginType;

    /**
     * 登陆信息
     * @var array
     */
    private $loginInfo = [];

    /**
     * 时间戳
     * @var int
     */
    private $timestamp;

    public function __construct($loginLogInfo = [])
    {
        $this->timestamp = time();
        if ($loginLogInfo) {
            @$this->userId = $loginLogInfo['userId'];
            //通过用户id获得登录者管理员个人信息
            $Admin = new Admin();
            $this->loginInfo = $Admin::getSelf(['userId' => $this->userId]);

            //初始化登陆信息
            $this->initLoginInfo();
        }
    }

    //初始化登陆信息
    private function initLoginInfo()
    {
        $this->loginType = $this->loginInfo['adminPower'];
    }

    //初始化日志信息
    private function initLog($info)
    {
        if (isset($info['userId'])) {
            $this->userId = $info['userId'];
        }
        $this->objectId = $info['id'];
    }


    /**
     * 获得日志必要信息
     * @return array
     */
    public function getInitInfo()
    {
        return [
            'userId'        => $this->userId,
            'objectId'      => $this->objectId,
            'createTime'    => $this->timestamp,
        ];
    }

    //添加微信系统必要信息，如appid等
    public function addSystemInfo($param)
    {
        //是否超管
        if ($this->loginType != 2) {
            return '30005';     //提示权限不够
        }

        //验证信息
        $validate = Loader::validate('SystemInfo');
        if (!$validate->check($param)) {
            return '50002';     //提示传参不符合格式要求
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

        //检验信息
        $validate = Loader::validate('SystemInfo');
        if (!$validate->check($param,$validate->getRule('modifyRule'))) {
            return '50002';
        }
        if (count($param) < 2) {
            return '50004';     //提示没有操作信息
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
    public function getSystemInfo($param)
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
        //验证传入的参数
        $validate = Loader::validate('Admin');
        if (!$validate->check($param,$validate->getRule('modifyRule'))) {
            return '50002';
        }

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

        //验证参数
        $validate = Loader::validate('Common');
        if (!$validate->check($param,$validate->getRule('pageRule'))) {
            return '50002';
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
        if (!$info->has('userId','post') || !$info->has('password','post')) {
            return '50003';         //没有传人相关信息
        }

        if (empty($info->post('userId')) || empty($info->post('userId'))) {
            return '50001';         //传参为空或传参不全
        }
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