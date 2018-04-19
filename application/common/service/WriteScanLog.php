<?php
namespace app\common\service;

use app\common\model\SceneLog;

class WriteScanLog
{

    /**
     * 写扫码日志
     * @param $msg
     */
    public static function writeScanLog($msg)
    {
        $SceneLog = new SceneLog();
        $SceneLog->insertInfo($msg);
    }


}