<?php
namespace app\common\service;


class FileLog
{
    protected static $type     = 'File';

    protected static $path     = RUNTIME_PATH.'logSelf'.DS;

    protected static $apartLevel;

    protected static $level = ['info','notice','error'];

    protected static $day;

    protected static $time;

    protected static $msg;

    protected static $ext = 'log';

    public static function init(array $info)
    {
        self::$type = $info['type']?:'File';
        self::$path = $info['path']?:self::$path;
        self::$apartLevel = $info['apart_level']?:[];
        self::formatPath();
        self::$time = date('Y-m-d H:i:s');
        self::$msg  = '--------------------------------------'.PHP_EOL.self::$time.PHP_EOL;
    }

    public static function formatPath()
    {
        $date       = date('y-m-d');
        $year       = substr($date,0,2);
        $month      = substr($date,3,2);
        self::$day  = substr($date,6,2);
        self::$path = self::$path.$year.$month.DS;
    }


    public static function write(string $msg,string $level = ''): bool
    {
        if (empty($level)) {
            $level = 'info';
        }
        //确定路径
        if ($level && in_array($level,self::$apartLevel)) {
            $lastName = '_'.$level;
        } else {
            $lastName = '';
        }
        self::$msg .= "[{$level}] ".$msg.PHP_EOL.PHP_EOL;
        if (!is_dir(self::$path)) {
            mkdir(self::$path,0777,true);
        }
        $fileName   = self::$path.self::$day.$lastName.'.'.self::$ext;
        file_put_contents($fileName,self::$msg,FILE_APPEND);
        self::$msg = '';
        return true;
    }




}