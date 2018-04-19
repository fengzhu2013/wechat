<?php
namespace app\content\validate;


use think\Validate;

class Article extends Validate
{
    /**
     * 增加文章字段验证
     * @var array
     */
    protected $addArticle = [
        'content'   => 'require',
        'summary'   => 'require|max:299',
        'title'     => 'require|max:64'
    ];

    /**
     * 获得文章列表规则
     * @var array
     */
    protected $listArticle = [
        'page'      => 'require|integer|gt:0',
        'pageSize'  => 'require|integer|gt:0',
    ];

    /**
     * 查看文章详情规则
     * @var array
     */
    protected $getArticle = [
        'articleId' => 'require|integer|gt:0',
    ];

    /**
     * 修改文章
     * @var array
     */
    protected $modifyArticle = [
        'articleId'     => 'require|integer|gt:0',
        'title'         => 'max:64',
        'summary'       => 'max:299',
    ];

    /**
     * 删除文章
     * @var array
     */
    protected $deleteArticle = [
        'articleId'     => 'require|integer|gt:0',
    ];


    /**
     * 验证添加一篇文章传人的参数
     * @param array $param
     * @return bool
     */
    public function checkAddArticle(array $param) :bool
    {
        if (!$this->verifyLen($param,3)) {
            return false;
        }
        return $this->check($param,$this->addArticle);
    }

    /**
     * 验证获得文章列表传入的参数
     * @param $param
     * @return bool
     */
    public function checkGetArticleList($param):bool
    {
        if (!$this->verifyLen($param,2)) {
            return false;
        }
        return $this->check($param,$this->listArticle);
    }

    /**
     * 验证查看文章详情规则信息
     * @param $param
     * @return bool
     */
    public function checkGetArticle($param):bool
    {
        if (!$this->verifyLen($param,1)) {
            return false;
        }
        return $this->check($param,$this->getArticle);
    }

    /**
     * 验证修改文章参赛
     * @param $param
     * @return bool
     */
    public function checkModifyArticle($param):bool
    {
        if (!$this->verifyLen($param,1,5)) {
            return false;
        }
        return $this->check($param,$this->modifyArticle);
    }

    /**
     * 验证删除文章参数
     * @param $param
     * @return bool
     */
    public function checkDeleteArticle($param):bool
    {
        if (!$this->verifyLen($param,1)) {
            return false;
        }
        return $this->check($param,$this->deleteArticle);
    }






}