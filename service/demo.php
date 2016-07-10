<?php
include_once "http.class.php";
set_time_limit(0);
//获取13位时间戳
function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

function mysubstr($string,$s,$e){
    $a = strpos($string,$s);
    $b = strpos($string,$e);
    return substr($string,$a+strlen($s),$b-$a-strlen($s));
}
/**
 * 时时彩购买列表（可能会改变cookie）
 */
//    $http = new http('http://52.68.32.109:8213/scpqa47904f_3625/ssc/order/list/?&_='.getMillisecond().'__autorefresh');
//    $http ->setHeader('Cookie: PHPSESSID=0745305626_1154_3625; mobiLogin=0; navNum=0; sysinfo=ssc%7C1%7Cb%7Cuc%7Cbeishu100; AC=6519335d2892e5364da0d5713b190cb0|1468038003');
//    $http ->setHeader('Host: 52.68.32.109:8213');
//    $http ->setHeader('Referer: http://52.68.32.109:8213/scpqa47904f_3625/index.htm?20902_20903_4.6.trunk_20150316');
//    $http ->setHeader('User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:47.0) Gecko/20100101 Firefox/47.0');
//    $http ->setHeader('X-Requested-With: XMLHttpRequest');
//    $http ->setHeader('ajax: true');
//    $data = array('action'=>'ajax');
//    $res = $http -> post($data);
//    var_dump($res);

/**
 * 获取购买列表数据
 */
//$json = '{"timesold":"20160709038","resultnum":["0","0","9","1","2"],"status":1,"timesnow":"20160709039","timeclose":326,"timeopen":416},"oddSet":"A","user_status":1,"drawStatus":"1","bigDrawStatus":"1","integrate":{"0000":9.916,"0001":9.916,"0002":9.916,"0003":9.916,"0004":9.916,"0005":9.916,"0006":9.916,"0007":9.916,"0008":9.916,"0009":9.916,"0010":9.916,"0011":9.916,"0012":9.916,"0013":9.916,"0014":9.916,"0015":9.916,"0016":9.916,"0017":9.916,"0018":9.916,"0019":9.916,"0020":9.916,"0021":9.916,"0022":9.916,"0023":9.916,"0024":9.916,"0025":9.916,"0026":9.916,"0027":9.916,"0028":9.916,"0029":9.916,"0030":9.916,"0031":9.916,"0032":9.916,"0033":9.916,"0034":9.916,"0035":9.916,"0036":9.916,"0037":9.916,"0038":9.916,"0039":9.916,"0040":9.916,"0041":9.916,"0042":9.916,"0043":9.916,"0044":9.916,"0045":9.916,"0046":9.916,"0047":9.916,"0048":9.916,"0049":9.916,"0050":1.984,"0051":1.984,"0052":1.984,"0053":1.984,"0060":1.984,"0061":1.984,"0062":1.984,"0063":1.984,"0070":1.984,"0071":1.984,"0072":1.984,"0073":1.984,"0080":1.984,"0081":1.984,"0082":1.984,"0083":1.984,"0090":1.984,"0091":1.984,"0092":1.984,"0093":1.984,"0100":1.984,"0101":1.984,"0102":1.984,"0103":1.984,"0110":1.984,"0120":1.984,"0130":9,"0140":70,"0150":70,"0160":70,"0170":13,"0180":13,"0190":13,"0200":2.8,"0210":2.8,"0220":2.8,"0230":2,"0240":2,"0250":2,"0260":2.2,"0270":2.2,"0280":2.2},"changlong":[["u7b2c2u7403","u96d9","6u671f"],["u7b2c1u7403","u96d9","4u671f"],["u7b2c3u7403","u5927","3u671f"],["u7b2c2u7403","u5c0f","2u671f"],["u7b2c5u7403","u5c0f","2u671f"]],"ballqueue_times":[9,4,4,4,2,8,2,0,3,2],"ballqueue_result":[["0",1],["8",2],["2",1],["5",1],["0",2],["5",1],["3",2],["1",1],["4",1],["1",1],["0",1],["9",1],["5",2],["0",1],["5",1],["1",1],["8",1],["4",1],["2",1],["6",1],["3",1],["2",1],["0",1],["2",1],["3",1]],"win":"0","sys":"ssc","version_number":"2","game_limit":{"00":[2,5000],"01":[2,50000],"02":[2,50000],"03":[2,10000],"04":[2,3000],"05":[2,5000],"06":[2,5000],"07":[2,5000],"08":[2,5000]},"success":true},"state":"1","errors":""}';
//$a = strpos($json,'"integrate":');
//$b = strpos($json,',"changlong"');
//$list = substr($json,$a+strlen('"integrate":'),$b-$a-strlen('"integrate":'));
//$aList = json_decode($list,true);
//var_dump($aList)

/**
 * 返回左侧用户信息(可能会改变cookie)
 * post/get
 */
//$http = new Http('http://52.68.32.109:8213/scpqa47904f_3625/ssc/order/leftInfo/?&_='.getMillisecond().'__ajax');
//$http ->setHeader('Cookie: PHPSESSID=57dc9d8646_1154_3625; mobiLogin=0; navNum=0; ValidatorAlert=; sysinfo=ssc%7C1%7Cb%7Cuc%7Cbeishu100; AC=106234e426b29da41978b8d81035bf79|1468049062');
//$http ->setHeader('Host: 52.68.32.109:8213');
//$http ->setHeader('Referer: http://52.68.32.109:8213/scpqa47904f_3625/index.htm?20902_20903_4.6.trunk_20150316');
//$http ->setHeader('User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:47.0) Gecko/20100101 Firefox/47.0');
//$http ->setHeader('X-Requested-With: XMLHttpRequest');
//$http ->setHeader('ajax: true');
//$data = array(
//    'act'=>'hand',
//    'sys'=>'ssc'
//);
//$res = $http -> post($data);
//var_dump($res);

//获取用户信息
//$json = 'HTTP/1.1 200 OK Date: Sat, 09 Jul 2016 07:26:43 GMT Content-Type: text/html; charset=utf-8 Transfer-Encoding: chunked Connection: keep-alive Expires: Thu, 19 Nov 1981 08:52:00 GMT Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0 Vary: Accept-Encoding Pragma: no-cache X-Frame-Options: SAMEORIGIN 28b {"data":{"user":{"account":"a009a009(A\u76e4)","credit":"10000","re_credit":"10000","total_amount":"0","odds_refresh":10,"game_limit":{"00":[2,5000],"01":[2,50000],"02":[2,50000],"03":[2,10000],"04":[2,3000],"05":[2,5000],"06":[2,5000],"07":[2,5000],"08":[2,5000]},"version_number":"10","new_orders":[],"fail_orders":"0"},"success":true},"state":"1","errors":""}锚锚锚 0 ';
//$userName = mysubstr($json,'{"account":"','","credit"');    //用户名
//$edu = mysubstr($json,',"credit":"','","re_credit"');    //信用额度
//$yue = mysubstr($json,',"total_amount":"','","odds_refresh"');    //信用余额
//var_dump($yue);

/**
 * 买
 */
$http = new Http('http://52.68.32.109:8213/scpqa47904f_3625/ssc/order/leftInfo/?post_submit&&_='.getMillisecond().'__ajax');
$http ->setHeader('Cookie: PHPSESSID=c21ef83aba_1154_3625; mobiLogin=0; navNum=0; ValidatorAlert=; sysinfo=ssc%7C1%7Cb%7Cuc%7Cbeishu100; AC=7e6fef7e2ded5587d0fa82eaef7b3cff|1468055947');
$http ->setHeader('Host: 52.68.32.109:8213');
$http ->setHeader('Referer: http://52.68.32.109:8213/scpqa47904f_3625/index.htm?20902_20903_4.6.trunk_20150316');
$http ->setHeader('User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:47.0) Gecko/20100101 Firefox/47.0');
$http ->setHeader('X-Requested-With: XMLHttpRequest');
$http ->setHeader('ajax: true');
$data = array(
    't'=>'005|2|1.984|5',
    'v'=>'18'
    );
$http->post($data);

?>