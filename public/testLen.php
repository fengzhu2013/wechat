<?php

$str1 = 'aaaa';

$str2 = '张三';

$str3 = '李四2';

var_dump(strlen($str1),strlen($str2),strlen($str3).'<br>');
var_dump(mb_strlen($str1,'utf-8'),mb_strlen($str2,'utf-8'),mb_strlen($str3,'utf-8').'<br>');