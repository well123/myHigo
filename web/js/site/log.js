var log = new Vue({
    el: "#logContent",
    data: {
        logList: {}
    },
    methods: {
        init: function () {
            setInterval(function () {
                $.getJSON('index.php?r=site/logs', '', function (data) {
                    log.logList = data;
                });
            }, 2000);
        }
    }
});
log.init();