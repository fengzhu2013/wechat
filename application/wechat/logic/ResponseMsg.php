<?php
namespace app\wechat\logic;

use app\wechat\model\AutoReply;
use app\wechat\model\Menu;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;

class ResponseMsg
{
    protected $result;

    public function __construct()
    {

    }

    public function getResult()
    {
        return $this->result;
    }

    //回复关注时信息
    public function responseSubscribe()
    {
        //从数据库中获取数据
        $AutoReply  = new AutoReply();
        $where      = ['reply_type' => '1'];
        $info       = $AutoReply->getOneInfoDesc($where);
        if (empty($info) || !isset($info['replyKey']) || empty($info['replyKey']) || !isset($info['replyValue']) || empty($info['replyValue'])) {
            //调用默认回复
            return $this->responseDefault();
        }
        return $this->handleKeyAndVal($info);
    }

    //处理回复信息的key与value
    protected function handleKeyAndVal($info)
    {
        //赋值记录日志信息
        @$this->result[$info['replyKey']] = $info['replyValue'];
        //处理信息
        switch ($info['replyKey']) {
            case 'news' :
                @$infoArr = json_decode($info['replyValue'],true);
                if (empty($infoArr)) {
                    return $this->responseDefault(true);
                }
                $items = [];
                foreach ($infoArr as $val) {
                    $items[] = new NewsItem($val);
                }
                return new News($items);
                break;
            case 'image':
                $mediaId = $info['replyValue'];
                return new Image($mediaId);
                break;
            case 'video':
                @$infoArr = json_decode($info['replyValue'],true);
                if (empty($infoArr)) {
                    return $this->responseDefault(true);
                }
                $other = [
                    'title'         => $infoArr['title'],
                    'description'   => $infoArr['description'],
                    'thumbMediaId'  => $infoArr['thumbMediaId']
                ];
                return new Video($infoArr['mediaId'],$other);
                break;
            case 'voice':
                $mediaId = $info['replyValue'];
                return new Voice($mediaId);
                break;
            case 'text':
                $content = $info['replyValue'];
                return new Text($content);
                break;
            default :
                return $this->responseDefault(true);
                break;
        }
    }

    //回复默认信息
    public function responseDefault($isMust = false)
    {
        //从数据库中获取数据
        $AutoReply  = new AutoReply();
        $where      = ['reply_type' => '2'];
        $info       = $AutoReply->getOneInfoDesc($where);
        if ($isMust || empty($info) || !isset($info['replyKey']) || empty($info['replyKey']) || !isset($info['replyValue']) || empty($info['replyValue'])) {
            //调用默认回复
            $this->result = ['text' => "感谢关注与支持"];
            return new Text("感谢关注与支持");
        }
        return $this->handleKeyAndVal($info);
    }

    /**
     * 回复关键词信息
     * @param $keywords
     * @return Text|Voice
     */
    public function responseKeywords($keywords)
    {
        //根据关键词获得信息
        $AutoReply  = new AutoReply();
        $info       = $AutoReply->getOneInfoByKeywordsDesc($keywords);
        if (empty($info)) {
            return $this->responseDefault();
        }
        //验证是否处于有效期
        $nowTime    = time();
        @$startTime = strtotime($info['startTime']);
        @$endTime   = strtotime($info['endTime']);
        if (empty($startTime) || empty($endTime)) {
            return $this->responseDefault();
        }
        if ($nowTime > $startTime && $nowTime < $endTime) {
            return $this->handleKeyAndVal($info);
        }
        return $this->responseDefault();
    }


    public function responseMenuKey($eventKey)
    {
        //获得key对应的value
        $Menu  = new Menu();
        $value = $Menu->getValueByKey($eventKey);
        if (empty($value)) {
            return $this->responseDefault();
        }

        //记录日志信息
        $this->result[$eventKey] = $value;

        @$valueArr = json_decode($value,true);
        if (!empty($valueArr)) {
            $count      = count($valueArr);
            $moreCount  = count($valueArr,1);
            if ($count !== $moreCount) {
                //图文消息
                $items = [];
                foreach ($valueArr as $val) {
                    $items[] = new NewsItem($val);
                }
                return new News($items);
            }
            if (1 === $count) {
                //图片
                @$mediaId = $valueArr['mediaId'];
                if (empty($mediaId)) {
                    return $this->responseDefault();
                }
                return new Image($mediaId);
            }
            //视频
            @$mediaId = $valueArr['mediaId'];
            if (empty($mediaId)) {
                return $this->responseDefault();
            }
            $other = [
                'title'         => $valueArr['title'],
                'description'   => $valueArr['description'],
                'thumbMediaId'  => $valueArr['thumbMediaId']
            ];
            return new Video($mediaId,$other);
        } else {
            //文本消息
            return new Text($value);
        }
    }

    public function responseScanKey($scanKey)
    {

    }





}