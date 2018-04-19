<?php
namespace app\scene\validate;

use think\Validate;

class Scene extends Validate
{
    protected $menu = ['1','2','3','4'];

    protected $modifyRule = [
        'id'        => 'require|number|gt:0',
        'status'    => 'acceptedSelf'
    ];

    /**
     * 分页参数验证规则
     * @var array
     */
    protected $pageInfo = [
        'page'      => 'integer|gt:0',
        'pageSize'  => 'integer|gt:0'
    ];

    protected $deleteScenes = [
        'ids'       => 'require',
    ];

    /**
     * 建议获得渠道列表参数
     * @param array $param
     * @return bool
     */
    public function checkGetSceneList(array $param):bool
    {
        if (!$this->verifyLen($param,-1,3)) {
            return false;
        }
        return $this->check($param,$this->pageInfo);
    }

    /**
     * 检验删除多个渠道参数信息
     * @param array $param
     * @return bool
     */
    public function checkDeleteScenes(array $param):bool
    {
        if (!$this->verifyLen($param,1)) {
            return false;
        }
        return $this->check($param,$this->deleteScenes);
    }







}