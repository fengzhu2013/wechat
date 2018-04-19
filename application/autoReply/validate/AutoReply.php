<?php
namespace app\autoReply\validate;

use app\common\logic\ArrayTool;
use think\Paginator;
use think\Validate;

class AutoReply extends Validate
{
    protected $all  = ['id','key','value','type','startTime','endTime','keywords'];

    protected $menu = ['text','image','news','video','voice'];

    /**
     * 添加默认规则，适合所有关注及默认
     * @var array
     */
    protected $aRule = [
        'value'         => 'require',
        'type'          => 'require|between:1,2',
        'key'           => 'require|acceptedSelf',
    ];

    protected $aKeywordsRule = [
        'type'          => 'require|between:3,4',
        'key'           => 'require|acceptedSelf',
        'value'         => 'require',
        'keywords'      => 'require|max:30',
        'startTime'     => 'require|date',
        'endTime'       => 'require|date',
    ];


    /**
     * 添加视频规则
     * @var array
     */
    protected $aVideoRule = [
        'title'         => 'require',
        'description'   => 'require',
        'mediaId'       => 'require',
        'thumbMediaId'  => 'require',
    ];

    /**
     * 设置图文规则
     * @var array
     */
    protected $aNewsRule = [
        'title'         => 'require',
        'description'   => 'require',
        'image'         => 'require',
        'url'           => 'require',
    ];

    protected $getSubAndDefRule = [
        'type'          => 'require|acceptedSelf',
    ];

    protected $modifySubAndDefInfoRule = [
        'id'            => 'require|number',
        'key'           => 'require|acceptedSelf',
        'value'         => 'require',
    ];

    protected $deleteReplyInfoRule = [
        'id'            => 'number|require',
    ];

    protected $getKeywordsInfoRule = [
        'id'            => 'number|require',
    ];

    protected $modifyKeywordsInfoRule = [
        'id'            => 'number|require',
        'type'          => 'between:3,4',
        'key'           => 'acceptedSelf',
        'startTime'     => 'date',
        'endTime'       => 'date',
        'keywords'      => 'max:30'
    ];


    /**
     * 验证设置关注及默认回复的验证，验证通过返回true
     * @param $param
     * @return bool
     */
    public function checkSet($param):bool
    {
        if (!$this->verifyLen($param,3)) {
            return false;
        }
        if (!$this->check($param,$this->aRule)) {
            return false;
        }
        return $this->checkKeyAndValue($param);
    }

    public function checkKeyAndValue($param):bool
    {
        switch ($param['key']) {
            case 'video':
                return $this->check($param['value'],$this->aVideoRule);
                break;
            case 'news':
                foreach ($param['value'] as $val) {
                    if (!$this->check($val,$this->aNewsRule)) {
                        return false;
                    }
                }
                return true;
                break;
            default :
                return true;
                break;
        }
    }

    /**
     * 验证获得关注或默认回复信息
     * @param $param
     * @return bool
     */
    public function checkGetSubAndDefInfo($param):bool
    {
        if (!$this->verifyLen($param,1)) {
            return false;
        }
        $this->menu = ['1','2'];
        if (!$this->check($param,$this->getSubAndDefRule)) {
            return false;
        }
        return true;
    }


    /**
     * 检验修改关注或默认回复时信息
     * @param $param
     * @return bool
     */
    public function checkModifySubAndDefInfo($param):bool
    {
        if (!$this->verifyLen($param,3)) {
            return false;
        }
        if (!$this->check($param,$this->modifySubAndDefInfoRule)) {
            return false;
        }
        return $this->checkKeyAndValue($param);
    }

    /**
     * 检验删除自动回复参数
     * @param $param
     * @return bool
     */
    public function checkDeleteReplyInfo($param):bool
    {
        if (!$this->verifyLen($param,1)) {
            return false;
        }
        return $this->check($param,$this->deleteReplyInfoRule);
    }

    /**
     * 建议添加关键词回复参数信息
     * @param $param
     * @return bool
     */
    public function checkAddKeywordsInfo($param):bool
    {
        if (!$this->verifyLen($param,6)) {
            return false;
        }
        if (!$this->check($param,$this->aKeywordsRule)) {
            return false;
        }
        return $this->checkKeyAndValue($param);
    }

    /**
     * 检验获得关键词信息的参数
     * @param $param
     * @return bool
     */
    public function checkGetKeywordsInfo($param):bool
    {
        if (!$this->verifyLen($param,1)) {
            return false;
        }
        return $this->check($param,$this->getKeywordsInfoRule);
    }

    /**
     * 验证修改关键词回复的信息
     * @param $param
     * @return bool
     */
    public function checkModifyKeywordsInfo($param):bool
    {
        if (!$this->verifyLen($param,1,8)) {
            return false;
        }
        if (!empty(ArrayTool::removeKey($param,$this->all))) {
            return false;
        }
        return $this->check($param,$this->modifyKeywordsInfoRule);
    }




}