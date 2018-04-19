<?php
namespace app\wechat\logic;

class Text
{
    protected $msg;

    protected $userId;

    protected $userLogType;

    protected $userLogCon;

    protected $timestamp;

    public function __construct($msg)
    {
        $this->msg          = $msg;
        $this->timestamp    = $msg['CreateTime'];
    }

    public function handleText($msg)
    {

    }



}