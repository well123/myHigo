<div id="logContent">
    <div class="caption">
        <label>Logs</label>
        <input type="button" :class="[!!systemStatus?'':'canNotClick']" @click="changeStatus()" :disabled="!systemStatus" value="STOP SYSTEM">
        <input type="button" :class="[!!systemStatus?'canNotClick':'']" :disabled="systemStatus" @click="changeStatus()" value="START SYSTEM">
    </div>
    <div>
        <ul>
            <li>时间</li>
            <li style="margin-left: 150px">内容</li>
        </ul>
        <ul v-for="item in logList">
            <li>{{item.create_time}}</li>
            <li>{{item.content}}</li>
        </ul>
    </div>
</div>
