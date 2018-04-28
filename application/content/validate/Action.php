<?php
namespace app\content\validate;


use think\Validate;

class Action extends Validate
{
    protected $menu     = [
        'view','message','timeLine'
    ];
    protected $view   = [
        'articleId'     => 'require|integer|gt:0',
        'openid'        => 'require|max:28',
    ];

    protected $share    = [
        'articleId'     => 'require|integer|gt:0',
        'openid'        => 'require|max:28',
        'type'          => 'require|acceptedSelf',
    ];

    /**
     * 检验记录浏览日志参数
     * @param array $param
     * @return bool
     */
    public function checkWriteView(array $param): bool
    {
        if (!$this->verifyLen($param,2)) {
            return false;
        }
        return $this->check($param,$this->view);
    }

    /**
     * 检验分享参数
     * @param array $param
     * @return bool
     */
    public function checkWriteShare(array $param): bool
    {
        if (!$this->verifyLen($param,2,5)) {
            return false;
        }
        return $this->check($param,$this->share);
    }





}