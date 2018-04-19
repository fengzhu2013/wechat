<?php
namespace app\content\logic;


use app\common\logic\ArrayTool;
use app\common\logic\BaseLogic;
use function Couchbase\fastlzCompress;
use think\Loader;

class Article extends BaseLogic
{
    public function __construct(array $loginLogInfo = [], $isVerifyType = false)
    {
        parent::__construct($loginLogInfo, $isVerifyType);
    }


    //获得文章列表
    public function getArticleList($param)
    {
        //从数据库中获得信息
        $Article    = new \app\content\model\Article();
        $data       = $Article->getPage($param,[],['article_id' => 'desc']);

        if (empty($data)) {
            return '50006';     //提示没有记录信息
        }
        foreach ($data as &$val) {
            //去掉content
            $val = ArrayTool::removeKey($val,'content');
            //格式化日期
            @$val['articleTime'] = date('Y-m-d H:i:s',$val['articleTime']);
        }
        $count      = $Article->getCount();
        return ['count' => $count,'data' => $data];
    }

    //获得一篇文章的详情
    public function getArticle($param)
    {
        //从数据库中获得信息
        $Article    = new \app\content\model\Article();
        $where      = $param;
        $info       = $Article::getSelf($where);

        if (empty($info)) {
            return '40004';     //提示标识符错误
        }
        //格式化日期
        @$info['articleTime'] = date('Y-m-d H:i:s',$info['articleTime']);
        return $info;
    }

    //修改一篇文章
    public function modifyArticle($param)
    {
        //根据id获得信息
        $Article    = new \app\content\model\Article();
        $where      = ['articleId' => $param['articleId']];
        $info       = $Article::getSelf($where);
        if (empty($info)) {
            return '50006';
        }
        //验证是否有更新的消息
        if (ArrayTool::compareArr($param,$info)) {
            return '50005';          //提示没有更新的信息
        }
        //修改信息
        $ret        = $Article->modifyInfo($param,$where);
        if (empty($ret)) {
            return false;
        }

        $this->initLog(['id' => $info['articleId']]);
        return true;
    }

    //删除一片文章
    public function deleteArticle($param)
    {
        //获得数据
        $Article    = new \app\content\model\Article();
        $where      = ['article_id' => $param['articleId']];
        $info       = $Article::getSelf($where);
        if (empty($info)) {
            return '50006';
        }
        //删除数据
        if (!$Article::destroy($where)) {
            return false;
        }
        $this->initLog(['id' => $param['articleId']]);
        return true;
    }

    //添加一篇文章
    public function addArticle($param)
    {
        //插入数据库
        $Article = new \app\content\model\Article();
        $param['articleTime'] = $this->timestamp;
        $ret     = $Article->insertOneInfo($param);

        if (empty($ret)) {
            return false;
        }
        //记录操作主键
        $this->initLog(['id' => $ret]);
        return true;
    }



}