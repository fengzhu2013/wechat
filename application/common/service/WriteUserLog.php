<?php
namespace app\common\service;

use app\common\model\UserLog;

class WriteUserLog
{
    const SUBSCRIBE         = 'subscribe';
    const UN_SUBSCRIBE      = 'unsubscribe';

    const SCAN_SUBSCRIBE    = 'scan_subscribe';
    const SCAN_OTHER        = 'scan_other';

    const MENU_CLICK        = 'menu_click';
    const MENU_VIEW         = 'menu_view';

    const TEXT              = 'text';
    const LOCATION          = 'location';

    const VIDEO             = 'video';
    const VOICE             = 'voice';

    const IMAGE             = 'image';
    const LINK              = 'link';



    public static function writeUserLog($userId,$type,$content,$time)
    {
        if (!is_array($content)) {
            $response['text'] = $content;
        } else {
            $response = $content;
        }
        $content = json_encode($response);
        $info = [
            'userLogType'   => $type,
            'userId'        => $userId,
            'userLogTime'   => $time,
            'userLogCon'    => $content,
        ];
        $UserLog = new UserLog();
        $UserLog->insetOneInfo($info);
    }
}