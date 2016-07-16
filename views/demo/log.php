<div id="logContent">
    <div><input type="button" @click="" value="START SYSTEM"> <input type="button" value="STOP SYSTEM"></div>
    <ul>
        <li>时间</li>
        <li style="margin-left: 150px">内容</li>
    </ul>
    <ul v-for="item in logList">
        <li>{{item.create_time}}</li>
        <li>{{item.content}}</li>
    </ul>
</div>
<!--<script>-->
<!--    var url = "ws://localhost:8080";-->
<!--    var ws = new WebSocket(url);-->
<!--    ws.onopen = function () {-->
<!--        console.log("握手成功，打开socket连接了。。。");-->
<!--        console.log("ws.send(Websocket opened)");-->
<!--        ws.send(("Websocket opened!"));-->
<!--    };-->
<!--    ws.onmessage = function (e) {-->
<!--        console.log("message:" + e.data);-->
<!--    };-->
<!--    ws.onclose = function () {-->
<!--        console.log("断开socket连接了。。。");-->
<!--    };-->
<!--    ws.onerror = function (e) {-->
<!--        console.log("ERROR:" + e.data);-->
<!--    };-->
<!--</script>-->
