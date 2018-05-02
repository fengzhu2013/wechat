<?php
namespace app\wechat\logic;

use app\common\logic\Common;
use app\common\service\WriteOpenidList;
use app\wechat\model\User;
use app\wechat\model\OpenidList;

/**
 * 处理关注及取消关注信息
 * Class Subscribe
 * @package app\wechat\logic
 */
class Subscribe
{
    /**
     * 微信服务器发送的信息
     * @var
     */
    protected $msg;

    protected $timestamp;

    protected $userId;

    public function __construct($msg)
    {
        $this->msg = $msg;
        $this->timestamp = $msg['CreateTime'];
    }


    /**
     * 处理关注时逻辑
     */
    public function handleSubscribe()
    {
        $openid = $this->msg['FromUserName'];
        //根据openid获得user表中信息
        $User = new User();
        $info = $User->getInfoByOpenid($openid);

        /**
         * 1、如果表中没有信息，表示第一次关注，插入两张表
         * 2、如果表中有信息，表示不是第一次关注，只是修改状态
         */

        //如果表中没有信息,表示第一次关注
        if (empty($info)) {
            //生成内部唯一标识符
            $userId = Common::createUserId();

            //记录日志信息
            $this->userId = $userId;

            /**存储两张表**/
            //存user表
            $info = ['userId' => $userId,'openid' => $openid,'status' => '1'];
            $User->insertInfo($info);

            //存user_list表
            $listInfo   = ['openid' => $openid,'status' => WriteOpenidList::UNTREATED,'createTime' => $this->timestamp];
            WriteOpenidList::writeLog($listInfo);
        } else {
            //记录日志信息
            $this->userId = $info['userId'];

            $where = ['id' => $info['id']];
            $info  = ['status' => '1'];
            $User->updateStatus($info,$where);

            //更改openid_list表中为5，表示需要更新信息
            $status = 5;
            $where  = ['openid' => $openid];
            $OpenidList = new OpenidList();
            $ret    = $OpenidList->updateStatus($status,$where);
            if (empty($ret)) {
                //删除数据
                $OpenidList::destroy($where);
                //插入数据
                $listInfo   = ['userId' => $this->userId,'openid' => $openid,'status' => '5'];
                $OpenidList->insertInfo($listInfo);
            }
        }
    }


    //处理取消关注时的逻辑
    public function handleUnSubscribe()
    {
        //取消关注，只是修改user表中的状态
        $User = new User();
        $info = $User->getInfoByOpenid($this->msg['FromUserName']);

        if ($info) {
            //记录日志
            $this->userId = $info['userId'];

            $where = ['id' => $info['id']];
            $info  = ['status' => '2'];
            $User->updateStatus($info,$where);
        }

    }

    //获得操作的日志所需要的信息
    public function getLogInfo()
    {
        return $this->userId;
    }









}
