<?php
namespace app\common\model;

use app\common\logic\FormatString;
use think\Model;

class Admin extends Model
{
    /**
     * 数据表属性
     */

    private $id;

    private $userId;

    private $adminName;

    private $password;

    private $adminPower;

    protected $createTime;

    private $status;


    public function __construct($data = [])
    {
        parent::__construct($data);
    }



}