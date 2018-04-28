<?php

    $arr = [
            0 => [
                'StartTime'     => '2018-07-11T20:00:00',
                'EndTime'       => '2018-07-11T21:00:00',
                'SessionId'     => '81f5166e-2995-41db-9101-755a457cb160',
                'IdStatus'      => 1,
                'Name'          => 'test1',
            ],
            1 => [
                'StartTime'     => '2018-05-12T08:00:00',
                'EndTime'       => '2018-05-12T09:00:00',
                'SessionId'     => '81f5166e-2995-41db-9101-755a457cb161',
                'IdStatus'      => 1,
                'Name'          => 'test2',
            ],
            2 => [
                'StartTime'     => '2018-05-12T18:00:00',
                'EndTime'       => '2018-05-12T19:00:00',
                'SessionId'     => '81f5166e-2995-41db-9101-755a457cb162',
                'IdStatus'      => 1,
                'Name'          => 'test3',
            ],
            3 => [
                'StartTime'     => '2018-06-1T23:00:00',
                'EndTime'       => '2018-06-2T01:00:00',
                'SessionId'     => '81f5166e-2995-41db-9101-755a457cb163',
                'IdStatus'      => 1,
                'Name'          => 'title4'
            ],
            4 => [
                'StartTime'     => '2018-06-2T23:00:00',
                'EndTime'       => '2018-06-3T01:00:00',
                'SessionId'     => '81f5166e-2995-41db-9101-755a457cb163',
                'IdStatus'      => 1,
                'Name'          => 'test5',
            ],
    ];

    var_dump(json_encode(formatInfo3($arr,false)));
    exit;

    function formatInfo($info,$bool = true)
    {
        $data = [];
        foreach ($info as $key => &$val) {
            //确定T
            $time           = ['SessionId' => $val['SessionId'],'Status' => $val['Status']];
            $startHour      = substr($val['StartTime'],strpos($val['StartTime'],'T')+1,5);
            $endHour        = substr($val['EndTime'],strpos($val['StartTime'],'T')+1,5);
            $hour           = $startHour.' - '.$endHour;
            $time['Time']   = $hour;
            //$Day['T'][]     = $time;
            //确定D
            $day            = substr($val['StartTime'],strrpos($val['StartTime'],'-') + 1,strpos($val['StartTime'],'T')-strrpos($val['StartTime'],'-')-1);

            //确定M
            $month          = intval(substr($val['StartTime'],5,2));

            //确定年
            $year           = intval(substr($val['StartTime'],2,2));
            $i = 0;
            $j = 0;
            $k = 0;
            if (isset($data['Y'])) {
                foreach ($data['Y'] as $key2 => &$val2) {
                    //判断年份是否存在
                    if ($val2['Year'] == $year.'年') {
                        foreach ($val2['M'] as $key3 => &$val3) {
                            if ($val3['Month'] == $month.'月') {
                                foreach ($val3['D'] as $key4 => &$val4) {
                                    if ($val4['Date'] == $day.'日') {
                                        $val4['T'][] = $time;
                                        $i = 1;         //表示找到归属
                                    }
                                }
                                if (0 === $i) {
                                    //表示time没有找到归属，没有想同的一天
                                    $newDay = ['Date' => $day.'日'];
                                    $newDay['T'][] = $time;
                                    $val3['D'][] = $newDay;
                                }
                                $j = 1;             //表示日找到归宿
                            }
                        }
                        if (0 === $j) {
                            $newMonth   = ['Month' => $month.'月'];
                            $T2[]       = $time;
                            $D2[]       = ['Date'  => $day.'日','T' => $T2];
                            $newMonth['D'] = $D2;
                            $val2["M"][]   = $newMonth;
                        }
                        $k = 1;
                    }
                }
                if (0 === $k) {
                    //不存在年，新建
                    $T3[] = $time;
                    $D3[] = ['Date'  => $day.'日','T' => $T3];
                    $M3[] = ['Month' => $month.'月','D' => $D3];
                    $Y3   = ['Year'  => $year.'年','M' => $M3];
                    $data['Y'][] = $Y3;
                }
            } else {
                //不存在，新建
                $T[] = $time;
                $D[] = ['Date'  => $day.'日','T' => $T];
                $M[] = ['Month' => $month.'月','D' => $D];
                $Y[] = ['Year'  => $year.'年','M' => $M];
                $data['Y'] = $Y;
            }
        }
        if (!$bool) {
            //去掉年
            return [ 'M' => $data['Y'][0]['M']];
        }
        return $data;
    }

    function formatInfo3($info,$bool = true)
    {
        //多维数组排序
        $info = sortArray($info,'StartTime','strtotime');
        $data = [];
        foreach ($info as $key => &$val) {
            //确定T
            $time           = ['SessionId' => $val['SessionId'],'IdStatus' => $val['IdStatus'],'Name' => $val['Name']];
            $startHour      = substr($val['StartTime'],strpos($val['StartTime'],'T')+1,5);
            $endHour        = substr($val['EndTime'],strpos($val['StartTime'],'T')+1,5);
            $hour           = $startHour.' - '.$endHour;
            //$time['Time']   = $hour;
            //$Day['T'][]     = $time;
            //确定D
            $day            = substr($val['StartTime'],strrpos($val['StartTime'],'-') + 1,strpos($val['StartTime'],'T')-strrpos($val['StartTime'],'-')-1);

            //确定M
            $month          = intval(substr($val['StartTime'],5,2));

            //确定年
            $year           = intval(substr($val['StartTime'],2,2));

            $monthAndDay    = $month.'月'.$day.'日';
            $i = 0;
            $j = 0;
            $k = 0;
            if (isset($data['Y'])) {
                foreach ($data['Y'] as $key2 => &$val2) {
                    //判断年份是否存在
                    if ($val2['Year'] == $year.'年') {
                        foreach ($val2['M'] as $key3 => &$val3) {
                            if ($val3['Month'] == $monthAndDay) {
                                $newDay = ['Date' => $hour];
                                $newDay['T'] = [0 => $time];
                                $val3['D'][] = $newDay;
                                /*foreach ($val3['D'] as $key4 => &$val4) {
                                    if ($val4['Date'] == $hour) {
                                        $val4['T'][] = $time;
                                        $i = 1;         //表示找到归属
                                    }
                                }
                                if (0 === $i) {
                                    //表示time没有找到归属，没有想同的一天
                                    $newDay = ['Date' => $hour];
                                    $newDay['T'][] = $time;
                                    $val3['D'][] = $newDay;
                                }*/
                                $j = 1;             //表示日找到归宿
                                //var_dump($data).'<br />';
                            }
                        }
                        if (0 === $j) {
                            //var_dump($val2);
                            $newMonth   = ['Month' => $monthAndDay];
                            $T2[0]       = $time;
                            $D2[0]       = ['Date'  => $hour,'T' => $T2];
                            $newMonth['D'] = $D2;
                            $val2["M"][]   = $newMonth;

                        }
                        $k = 1;
                    }
                }
                if (0 === $k) {
                    //不存在年，新建
                    $T3[0] = $time;
                    $D3[0] = ['Date'  => $hour,'T' => $T3];
                    $M3[0] = ['Month' => $monthAndDay,'D' => $D3];
                    $Y3    = ['Year'  => $year.'年','M' => $M3];
                    $data['Y'][] = $Y3;
                }
            } else {
                //不存在，新建
                $T[] = $time;
                $D[] = ['Date'  => $hour,'T' => $T];
                $M[] = ['Month' => $monthAndDay,'D' => $D];
                $Y[] = ['Year'  => $year.'年','M' => $M];
                $data['Y'] = $Y;
            }
        }
        if (!$bool) {
            //去掉年
            return [ 'M' => $data['Y'][0]['M']];
        }
        return $data;
    }



    function formatInfo2($info,$bool = true)
    {
        //多维数组排序
        $info = sortArray($info,'StartTime','strtotime');
        $data = [];
        foreach ($info as $key => &$val) {
            //确定T
            $time           = ['SessionId' => $val['SessionId'],'IdStatus' => $val['IdStatus'],'Name' => $val['Name']];
            $startHour      = substr($val['StartTime'],strpos($val['StartTime'],'T')+1,5);
            $endHour        = substr($val['EndTime'],strpos($val['StartTime'],'T')+1,5);
            $hour           = $startHour.' - '.$endHour;
            $time['Time']   = $hour;
            //$Day['T'][]     = $time;
            //确定D
            $day            = substr($val['StartTime'],strrpos($val['StartTime'],'-') + 1,strpos($val['StartTime'],'T')-strrpos($val['StartTime'],'-')-1);

            //确定M
            $month          = intval(substr($val['StartTime'],5,2));

            //确定年
            $year           = intval(substr($val['StartTime'],2,2));
            $i = 0;
            $dateAndMonth = $month.'月'.$day.'日';
            if (isset($data['M'])) {
                foreach ($data['M'] as $key2 => &$val2) {
                    if ($val2['DateAndMonth'] == $dateAndMonth) {
                        $val2['T'][] = $time;
                        $i = 1;
                    }
                }
                if (0 === $i) {
                    //新建
                    $T2 = [ 0 => $time];
                    $data['M'][] = ['DateAndMonth'  => $dateAndMonth,'T' => $T2];
                }
            } else {
                //不存在，新建
                $T[] = $time;
                $D[] = ['DateAndMonth'  => $dateAndMonth,'T' => $T];
                $data['M'] = $D;
            }
        }
        return $data;
    }

    function sortArray($arr,$key,$callback = false)
    {
        $newArr = [];
        $count  = count($arr);

        for($i=0;$i<$count-1;$i++) {
            for ($j=0;$j<$count-$i-1;$j++) {
                if ($callback) {
                    $left  = call_user_func($callback,$arr[$j][$key]);
                    $right = call_user_func($callback,$arr[$j+1][$key]);
                } else {
                    $left = $arr[$j][$key];
                    $right = $arr[$j+1][$key];
                }
                if ($right < $left) {
                    $newArr = $arr[$j];
                    $arr[$j] = $arr[$j+1];
                    $arr[$j+1] = $newArr;
                }
            }
        }
        return $arr;
    }





    if (isset($_SERVER['https'])) {
        $https = 'https://';
    } else {
        $https = 'http://';
    }
    echo $https;
?>
<?php
    echo $https;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>test</title>
</head>
<body>
    <form action="http://edu.natapp1.cc/wechat/public/admin/index/systemLogin" method="post">
        userId:<input type="text" placeholder="userId" name="userId"><br>
        password:<input type="text" placeholder="password" name="password"><br>
        <input type="submit" value="systemLogin">
    </form>
</body>
</html>
<?php
    echo $https;
?>