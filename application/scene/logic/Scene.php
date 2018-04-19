<?php
namespace app\scene\logic;

use app\common\logic\ArrayTool;
use app\common\logic\BaseLogic;
use app\common\logic\File;
use app\scene\model\SceneInfo;
use app\wechat\service\QrCode;
use function Couchbase\fastlzCompress;
use think\Loader;

class Scene extends BaseLogic
{
    const QRCODE_PATH = ROOT_PATH.'public'.DS.'qrcode'.DS;

    const ZIP_PATH = ROOT_PATH.'public'.DS.'zip'.DS;

    protected $actionMsg;

    //必须的键
    protected $needKeys = [0,8];

    public function __construct($loginLogInfo = [])
    {
        parent::__construct($loginLogInfo);
        $this->actionMsg = [];
    }

    //批量导入渠道信息
    public function addScenes($request)
    {
        //获得上传的文件
        $sceneFile = $request->file('sceneFile');
        //移动到根目录/public/uploads/ 目录下
        $info = $sceneFile->move(ROOT_PATH . 'public' . DS . 'uploads');
        if (!$info) {
            $this->initActionMsg(['error' => $sceneFile->getError()]);
            return '60001';     //提示文件上传失败
        }
        //把文件信息读入一个数组
        $fileInfo = File::readFile($info);
        if (empty($fileInfo)) {
            return '60002';     //提示读取文件失败，请重新上传
        }
        //检验字段是否符合要求
        if (!ArrayTool::checkNeedKey($fileInfo,$this->needKeys)) {
            return '40005';     //文件内容不符合规定，没有通过必要字段检测
        }
        //增加的记录数
        $count = count($fileInfo);

        $Scene = new \app\scene\model\Scene();
        //获得数据库中已有的待领取的二维码数量
        $waitCount = $Scene->getWaitCount();

        //如果库中二维码数量不够，去微信中获取
        if ($count > $waitCount) {
            //获得最后一个永久二维码的scene_id
            $lastSceneId = $Scene->getLastSceneId();
            if ($lastSceneId > 10000 || $lastSceneId + $count - $waitCount > 10000) {
                return '40006';         //提示数量不够
            }
            //从微信服务器中获取二维码
            $qrCode = $this->getOrCodeFromWechat($lastSceneId+1,$count-$waitCount);
            //处理信息，(保持到本地)，返回需要的信息
            $needInfo = $this->handleQrCodeInfo($qrCode);

            //存入数据库
            foreach ($needInfo as $need) {
                if (!$Scene->data($need)->isUpdate(false)->saveSelf()) {
                    $this->initActionMsg($need);
                }
            }
        }

        //从库中获取到数量的待领取的二维码
        $countInfo = $Scene->getCountInfo($count);

        if (count($countInfo) !== $count) {
            return '20003';         //提示系统出了问题
        }

        $SceneInfo = new SceneInfo();
        //处理渠道信息
        $keys = $SceneInfo->getKeys();
        //先换键
        foreach ($fileInfo as $key => $val) {
            $val['createTime'] = $this->timestamp;
            $sceneInfo[$countInfo[$key]['sceneId']] = $val;
        }
        foreach ($sceneInfo as &$val) {
            foreach ($val as $secKey => $secVal) {
                unset($val[$secKey]);
                $val[$keys[$secKey]] = $secVal;
            }
            //去掉每条记录为空的元素
            $val = array_filter($val);
        }
        //批量插入信息
        $ret = $SceneInfo->addMore($sceneInfo);
        //记录日志
        $this->initActionMsg(['insertMore' => $ret]);
        //根据插入的数据更新渠道表
        $i = 0;
        $j = 0;
        foreach ($ret as $retKey => $retVal) {
            if ($retVal && $Scene->updateOne(['status' => 2],['sceneId' => $retKey])) {
                $i++;
            } else {
                $j++;
            }
        }
        $msg = $this->formatMsg($count,$j,$i);
        $this->initActionMsg(['return' => $msg]);
        return ['msg' => $msg];
    }

    /**
     * 从微信服务器中获取一定数量的二维码
     * @param int $start
     * @param int $count
     * @return array
     */
    protected function getOrCodeFromWechat(int $start,int $count):array
    {
        $qrCode = new QrCode();
        $info   = [];
        for($i=0;$i<$count;$i++) {
            $res = $qrCode->getForever($start+$i);
            if (empty($res)) {
                return $info;
            }
            $info[] = $res;
        }
        return $info;
    }

    /**
     * 处理成本地数据
     * @param array $info
     * @return array
     */
    protected function handleQrCodeInfo(array $info):array
    {
        foreach ($info as &$val) {
            $fileName = $val['sceneId'].'.jpg';
            file_put_contents(self::QRCODE_PATH.$fileName,file_get_contents($val['url']));
            $val['status'] = 1;
            $val['url']    = 'qrcode'.DS.$fileName;
        }
        return $info;
    }

    //记录操作信息数组
    private function initActionMsg($msg)
    {
        $this->actionMsg[] = $msg;
    }

    /**
     * 获得操作的记录信息
     * @return array
     */
    public function getActionMsg()
    {
        return $this->actionMsg;
    }

    /**
     * 处理返回信息
     * @param int $count
     * @param int $j
     * @param int $i
     * @return string
     */
    public function formatMsg(int $count,int $j,$i = 0):string
    {
        $msg = "总记录{$count}条，";
        if ($j) {
            $msg = "失败了{$j}条，";
        }
        $i = $i?$i:$count - $j;
        if ($i) {
            $msg = "成功了{$i}条！";
        }
        return $msg;
    }

    //获得一定数量的二维码
    public function getOrCodeCount($param)
    {
        if (!isset($param['count']) || empty($param['count'])) {
            return '50002';
        }
        $count = intval($param['count']);
        //获得数据库中最后一个sceneId
        $Scene = new \app\scene\model\Scene();
        $lastSceneId = $Scene->getLastSceneId();
        if ($lastSceneId > 10000 || $lastSceneId + $count > 10000) {
            return '40006';     //提示数量不够
        }
        //从微信服务器中获取二维码
        $qrCode = $this->getOrCodeFromWechat($lastSceneId+1,$count);
        //处理信息，(保持到本地)，返回需要的信息
        $needInfo = $this->handleQrCodeInfo($qrCode);

        //存入数据库
        $j = 0;
        foreach ($needInfo as $need) {
            if (!$Scene->data($need)->isUpdate(false)->saveSelf()) {
                $this->initActionMsg($need);
                $j++;
            }
        }
        $msg = $this->formatMsg($count,$j);
        $this->initActionMsg(['return' => $msg]);
        return ['msg' => $msg];
    }

    //添加单个渠道信息
    public function addOneScene($param)
    {
        $Scene = new \app\scene\model\Scene();
        //获得数据库中已有的待领取的二维码数量
        $waitCount = $Scene->getWaitCount();

        //从微信服务器中获取
        if ($waitCount < 1) {
            //获得最后一个永久二维码的scene_id
            $lastSceneId = $Scene->getLastSceneId();
            if ($lastSceneId == 10000) {
                return '40006';         //提示数量不够
            }
            //从微信服务器中获取二维码
            $qrCode = $this->getOrCodeFromWechat($lastSceneId+1,1);
            //处理信息，(保持到本地)，返回需要的信息
            $needInfo = $this->handleQrCodeInfo($qrCode);
            //插入数据库
            foreach ($needInfo as $need) {
                if (!$Scene->data($need)->isUpdate(false)->saveSelf()) {
                    $this->initActionMsg($need);
                    return '20002';
                }
            }
        }

        //从数据库中取出一个待领取二维码
        $countInfo = $Scene->getCountInfo(1);
        if (empty($countInfo)) {
            return '20003';
        }

        $param['createTime'] = $this->timestamp;
        //换键['sceneId' => []]
        $info[$countInfo[0]['sceneId']] = $param;

        $SceneInfo = new SceneInfo();

        $this->initLog(['id' => $countInfo[0]['id']]);
        //添加
        $result = $SceneInfo->addOne($info);
        if (!$result) {
            return false;
        }
        $res = $Scene->updateOne(['status' => 2],['sceneId' => $countInfo[0]['sceneId']]);
        if (!$res) {
            return false;
        }
        return true;
    }

    //获得渠道列表
    public function getSceneList($param)
    {
        //先获取二维码表的列表
        $Scene = new \app\scene\model\Scene();
        @$sceneArr = $Scene->getHadInfo($param['page'],$param['pageSize']);

        if (empty($sceneArr)) {
            return '50006';     //提示没有记录信息
        }
        //获得总记录数
        $count = $Scene->getHadCount();

        $SceneInfo = new SceneInfo();
        foreach ($sceneArr as &$val) {
            //通过sceneId获得sceneInfo信息
            $sceneInfo = $SceneInfo->getInfo($val['sceneId']);
            $val = $this->handleSceneAndSceneInfo($val,$sceneInfo);
        }

        $ret = [
            'count' => $count,
            'data'  => $sceneArr,
        ];
        return $ret;
    }

    /**
     * 合并scene和sceneInfo信息
     * @param array $scene
     * @param array $sceneInfo
     * @return array
     */
    protected function handleSceneAndSceneInfo(array $scene,array $sceneInfo):array
    {
        unset($scene['url']);
        if (!$sceneInfo) {
            return $scene;
        }
        foreach ($sceneInfo as $item) {
            $scene[$item['sceneKey']] = $item['sceneValue'];
        }
        return $scene;
    }

    //删除单个渠道，其实是修改状态
    public function deleteOneScene($param)
    {
        //更具id获得信息
        $Scene = new \app\scene\model\Scene();
        $info = $Scene::get(['id' => $param['id']]);
        if (!$info) {
            return '40004';     //提示标识符错误
        }
        if ($info->status == '3' || (isset($param['status']) && $param['status'] == $info->status)) {
            return '50007';     //提示已是该状态，不用重复操作
        }
        $info->status = 3;
        if (isset($param['status'])) {
            $info->status = $param['status'];
        }
        if (!$info->save()) {
            return false;
        }
        return true;
    }

    //批量删除
    public function deleteScenes($param)
    {
        if (strpos($param['ids'],',') !== false) {
            $params = explode(',',$param['ids']);
        } else {
            if (!is_numeric($param['ids'])) {
                return '50002';
            }
            $params = [$param['ids']];
        }
        //构建成更新数组
        foreach ($params as $key => $val) {
            $info[$key] = ['id' => $val,'status' => 3];
        }
        //更新数据
        $Scene = new \app\scene\model\Scene();
        $ret = $Scene->saveAll($info);
        if (!$ret) {
            return '20003';
        }

        //构造记录信息
        $i = 0;
        $j = 0;
        foreach ($ret as $val) {
            $retArr = $val->toArray();
            if ($retArr['status'] === 3) {
                $i++;
                $msg[$retArr['id']] = true;
            } else {
                $j++;
                $msg[$retArr['id']] = false;
            }
        }
        $this->initActionMsg($msg);
        $retMsg = $this->formatMsg(count($param),$j,$i);
        return ['return' => $retMsg];
    }


    //修改信息
    public function modifyScene($param)
    {
        $Scene = new \app\scene\model\Scene();
        $info = $Scene::getSelf(['id' => $param['id']]);
        if (empty($info)) {
            return '40004';     //提示标识符错误
        }

        //获得这条记录的scene_info信息
        $SceneInfo = new SceneInfo();
        $sceneInfo = $SceneInfo->getInfo($info['sceneId']);
        if (empty($sceneInfo)) {
            return '50006';     //提示没有记录信息
        }

        //两个信息资源合并成一个数组
        $infoArr = $this->handleSceneAndSceneInfo($info,$sceneInfo);
        //判断是否有更新的信息
        $update = [];
        $insert = [];
        foreach ($param as $key => $val) {
            if (!isset($infoArr[$key])) {
                $insert[$key] = $val;
            } elseif ($infoArr[$key] != $val) {
                $update[$key] = $val;
            }
        }
        if (empty($update) && empty($insert)) {
            return '50005';     //提示没有需要更新的记录
        }
        //记录操作数据的id
        $this->initLog(['id' => $infoArr['id']]);
        //开始插入或更新
        if ($insert) {
            $result = $SceneInfo->addOne([$info['sceneId']=>$insert]);
        }
        //执行了插入但失败了
        if (isset($result) && !$result) {
            return false;
        }

        //update
        if ($update) {
            $res = $SceneInfo->updateInfo($info['sceneId'],$update);
        }
        //执行了更新但失败了
        if (isset($res) && !$res) {
            return false;
        }
        return true;
    }

    //批量打包下载渠道二维码
    public function downloadScenes($request)
    {
        $zip = new \ZipArchive();
        $zipName = time().'_downloadScene.zip';
        //新建压缩包
        if ($zip->open(self::ZIP_PATH.$zipName,\ZipArchive::CREATE) !== true) {
            return false;
        }
        //获得所有正常二维码的场景号
        $Scene = new \app\scene\model\Scene();
        $sceneIds = $Scene->getAllByStatus(2);
        if (!$sceneIds) {
            return '50006';     //没有记录信息
        }
        $sceneIdArr = array_column($sceneIds,'sceneId');
        //把图片一张张压缩进去
        $handle = opendir(self::QRCODE_PATH);
        while (($filename = readdir($handle)) !== false) {
            if ($filename != '.' && $filename != '..') {
                $ext  = getExtension($filename);
                $name = getName($filename);
                if ($ext && ($ext == 'jpg' || $ext == 'jpeg') && in_array($name,$sceneIdArr)) {
                    $zip->addFile(self::QRCODE_PATH.$filename,$filename);
                }
            }
        }
        $zip->close();
        return ['url'   => $request->root(true).DS.'zip'.DS.$zipName];
    }





}