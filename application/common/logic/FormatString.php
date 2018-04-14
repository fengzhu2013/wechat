<?php
namespace app\common\logic;

class FormatString
{
    protected $string;

    protected $arr;

    protected $result;

    protected $format = ['_'];

    protected $address = [];

    public function __construct($value = null)
    {
        if (is_string($value)) {
            $this->string = $value;
        }
        if (is_array($value)) {
            $this->arr = $value;
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        unset($this->string,$this->arr);
    }

    public function main()
    {
        if ($this->string) {
            $this->result = $this->format();
        } elseif (!empty($this->arr)) {
            foreach ($this->arr as $val) {
                $this->string = $val;
                $this->result[] = $this->format();
            }
        }
        return $this->toString();
    }

    public function format($string = null)
    {
        if ($string)
            $this->string = $string;
        //首字母大写
        //$this->string = ucfirst($this->string);
        //获取特殊字符出现的次数
        $count = $this->getCount();
        for ($i = 0; $i < $count; $i++) {
            //获取第一个特殊字符出现的位置
            $first = $this->getFirstAddress();
            //先截取至后一位的字符串
            $substr = substr($this->string, 0, $first + 2);
            //获取到后半段
            $end = substr($this->string, $first + 2);
            //最后一个字符串大写
            $substr = strrev(ucfirst(strrev($substr)));
            //去掉特殊字符
            $substr = substr_replace($substr, null, $first, 1);
            //替换掉原字段
            $this->string = $substr . $end;
        }
        $this->address = [];
        return $this->string;
    }

    /**
     * 格式化数组的键
     * @param $array
     * @param $type
     * @return array
     */
    public function formatArrKey($array,$type = 'o')
    {
        $arr = [];
        foreach ($array as $key => $val) {
            if ($type === 'i') {
                $newKey = $this->formatIn($key);
            } else {
                $newKey = $this->format($key);
            }
            $arr[$newKey] = $val;
        }
        return $arr;
    }




    public function formatArrVal(array $arr,$type = 'o')
    {
        foreach ($arr as &$val) {
            if ($type == 'i') {
                $val = $this->formatIn($val);
            } else {
                $val = $this->format($val);
            }
        }
        return $arr;
    }

    public function formatIn($string)
    {
        $stringArr = str_split($string);
        foreach ($stringArr as &$val) {
            if (preg_match('/[A-Z]/', $val)) {
                $val = '_'.strtolower($val);
            }
        }
        $this->result = implode('',$stringArr);
        return $this->result;
    }

    /**
     * 获得地址信息
     */
    private function getAddressInfo()
    {
        foreach ($this->format as $val) {
            preg_match_all('/' . $val . '/', $this->string, $this->address[], PREG_OFFSET_CAPTURE);
        }
    }

    /**
     * 获得第一个字符出现的位置
     * @return mixed
     */
    private function getFirstAddress()
    {
        foreach ($this->format as $val) {
            $info = strpos($this->string, $val);
            if (!is_bool($info))
                $address[] = $info;
        }
        sort($address);
        return $address[0];
    }

    /**
     * 获得特殊字符出现的次数
     */
    private function getCount()
    {
        $this->getAddressInfo();
        if (empty($this->address[0][0])) {
            return 0;
        } else {
            $this->handleAddress();
            return count($this->address);
        }
    }

    private function handleAddress()
    {
        //只需要具体的地址
        foreach ($this->address as $val) {
            foreach ($val as $secVal) {
                foreach ($secVal as $thirdVal) {
                    $address[] = $thirdVal[1];
                }
            }
        }
        if (isset($address)) {
            sort($address);
            $this->address = $address;
        } else {
            $this->address = [];
        }

    }

    public function toString()
    {
        return $this->result;
        //var_dump($this->address);
    }

}