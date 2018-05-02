<?php
namespace app\god\logic;


use app\common\service\FileLog;
use app\god\model\OpenidList;
use app\god\model\UserInfo;
use app\wechat\service\User;
use think\Log;

class God
{
    protected $i = 1;

    public function __construct()
    {
        FileLog::init([
            'type'          => 'File',
            'path'          => RUNTIME_PATH.'openid'.DS,
            'apart_level'   => ['openidError','wxError','modifyUserError','insertUserInfoError','action'],
        ]);
    }

    /**
     * 获得用户信息
     * @return bool
     */
    public function getWechatUserInfo()
    {
        //记录操作开始了一轮
        $num            = 5;
        $OpenidList     = new OpenidList();
        //在openid中获取10个未处理的openid
        $where          = ['status' => 1];
        $info           = $OpenidList->getOpenidList($where,$num);
        if (empty($info)) {
            return true;
        }

        $rand           = rand(100,999).'--';
        FileLog::write($rand.'开始执行第'.$this->i.'次，操作了'.$num.'条'.PHP_EOL,'action');

        $openidArr      = array_column($info,'openid');
        ///把状态改成处理中
        $result         = $OpenidList->modifyStatus($openidArr,'2');
        if (!$result) {
            FileLog::write(var_export($openidArr,true),'openidError');
            return false;
        }
        //从微信中获取用户信息
        $User           = new User();
        $userInfoList   = $User->getAllUserInfo($openidArr);
        if (!isset($userInfoList['user_info_list']) || empty($userInfoList['user_info_list'])) {
            $msg = var_export($openidArr,true).var_export($userInfoList,true);
            Log::write($msg,'wxError');
            return false;
        }
        //获得用户userId
        $UserObj        = new \app\god\model\User();
        $userIdArr      = $UserObj->getUserIdByOpenidArr($openidArr);
        $openidToUserId = array_flip($userIdArr);

        //处理插入及更新信息
        $needInfo       = $this->handleInfo($userInfoList['user_info_list'],$userIdArr);
        $UserInfo       = new UserInfo();

        //开始插入及更新
        foreach ($needInfo['insert'] as $key => $val) {
            $ret = $UserInfo->insertAllInfoSelf($val,'id',true);
            if ($ret) {
                //更新user表
                $where = ['userId' => $key];
                $data  = $needInfo['update'][$key];
                $res   = $UserObj->updateInfo($data,$where);
                if (empty($res)) {
                    $msg = $key.var_export($data,true);
                    FileLog::write($msg,'modifyUserError');
                }
                //成功的openid
                $successOpenid[] = $openidToUserId[$key];
            } else {
                //插入失败
                FileLog::write(var_export($val,true),'insertUserInfoError');
                //失败的openid
                $failedOpenid[] = $openidToUserId[$key];
            }
        }

        //更新openidList表中 status状态
        //操作成功的
        if (!empty($successOpenid)) {
            $response = $OpenidList->modifyStatus($successOpenid,'3');
            if (!$response) {
                return false;
            }
        }
        if (!empty($failedOpenid)) {
            $response = $OpenidList->modifyStatus($failedOpenid,'4');
            if (!$response) {
                return false;
            }
        }
        //记录操作成功了一轮
        FileLog::write($rand.'成功执行了'.$this->i.'次，操作了'.$num.'条'.PHP_EOL,'action');

        $this->i++;
        $this->getWechatUserInfo();

    }

    /**
     * 处理需要的信息，一个是更新信息，['userId' => [],]
     * 一个是新增消息['userId' => [[],],]
     * @param array $userInfoList
     * @param array $userIdArr
     * @return array
     */
    protected function handleInfo(array $userInfoList,array $userIdArr):array
    {
        $insertInfo = [];
        $updateInfo = [];
        foreach ($userInfoList as $userInfo) {
            $userId = $userIdArr[$userInfo['openid']];
            $insertArr = [];
            $updateArr = [];
            foreach ($userInfo as $key => $val) {
                if ('subscribe' === $key) {
                    $updateArr['status']  = $val?:3;       //subscribe仍然进入插入数据
                }
                if ('tagid_list' === $key) {
                    $val = json_encode($val);
                }
                if ('unionid' === $key) {
                    $updateArr['unionid'] = $val;
                    continue;       //unionid不进入插入数据
                }
                if ('openid' !== $key) {
                    $insertArr[] = ['key' => $key,'value' => $val,'userId' => $userId];
                }
            }
            $insertInfo[$userId] = $insertArr;
            $updateInfo[$userId] = $updateArr;
        }
        $ret = [
            'insert'    => $insertInfo,
            'update'    => $updateInfo,
        ];
        return $ret;
    }


}