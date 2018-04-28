<?php
namespace app\comment\validate;


use think\Validate;

class Comment extends Validate
{
    protected $mass = [
        'startDate'         => 'require|date',
        'endDate'           => 'date',
    ];

    protected $page = [
        'page'              => 'require|integer|gt:0',
        'pageSize'          => 'require|integer|gt:0',
    ];


    protected $checkGetComByArticle = [
        'msgid'             => 'require|max:12',
        'page'              => 'require|integer|gt:0',
        'pageSize'          => 'require|integer|gt:0',
    ];


    /**
     * 检验获得群发日志信息
     * @param array $param
     * @return bool
     */
    public function checkGetMassLog(array $param): bool
    {
        if (!$this->verifyLen($param,0,3)) {
            return false;
        }
        return $this->check($param,$this->mass);
    }

    /**
     * 验证获得文章列表参数
     * @param array $param
     * @return bool
     */
    public function checkGetArticleList(array $param): bool
    {
        if (!$this->verifyLen($param,2)) {
            return  false;
        }
        return $this->check($param,$this->page);
    }

    /**
     * checkGetComByArticle
     * @param array $param
     * @return bool
     */
    public function checkGetComByArticle(array $param): bool
    {
        if  (!$this->verifyLen($param,3)) {
            return false;
        }
        return $this->check($param,$this->checkGetComByArticle);
    }



}