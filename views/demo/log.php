<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/11 0011
 * Time: 16:22
 */
?>
<div>

</div>
<script>
    var url = "ws://localhost:8080";
    var ws = new WebSocket(url);
    ws.onopen = function () {
        console.log("握手成功，打开socket连接了。。。");
        console.log("ws.send(Websocket opened)");
        ws.send(("Websocket opened!"));
    };
    ws.onmessage = function (e) {
        console.log("message:" + e.data);
    };
    ws.onclose = function () {
        console.log("断开socket连接了。。。");
    };
    ws.onerror = function (e) {
        console.log("ERROR:" + e.data);
    };
</script>
