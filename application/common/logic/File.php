<?php
namespace app\common\logic;


class File
{

    public static function readFile($info)
    {
        @$ext = strtolower($info->getExtension());
        switch ($ext) {
            case 'csv':
                $result = self::readCsvFileToArray($info);
                break;
            default:
                $result = [];
                break;
        }
        return $result;
    }

    public static function readCsvFileToArray($info)
    {
        //获取文件路径，不带文件名和最后的斜杠
        $path = $info->getPath();
        //获得文件名
        $fileName = $info->getFilename();
        $filePath = $path.'\\'.$fileName;
        //读文件到一个字符串，需要转换成utf-8格式
        $fileString = file_get_contents($filePath);
        if (!mb_check_encoding($fileString,'utf-8')) {
            $fileString = iconv('gb2312','utf-8',$fileString);
        }

        //把字符串以分行分割符分割成数组
        $stringArr = array_filter(explode(PHP_EOL,$fileString));
        foreach ($stringArr as $key => $val) {
            $stringArr[$key] = explode(',',$val);
        }
        //unlink($filePath);
        return $stringArr;
    }

}