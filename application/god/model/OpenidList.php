<?php
namespace app\god\model;


use think\Model;

class OpenidList extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function getOpenidList(array $where): array
    {
        $where = self::$formatObj->formatArrKey($where,'i');
        
    }



}