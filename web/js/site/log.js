var log = new Vue({
    el: "#logContent",
    data: {
        logList: {},
        systemStatus: true
    },
    methods: {
        init: function () {
            $.getJSON('index.php?r=site/get-status', '', function (data) {
                log.systemStatus = data;
            })
            setInterval(function () {
                $.getJSON('index.php?r=site/logs', '', function (data) {
                    log.logList = data;
                });
            }, 2000);
        },
        resetTime: function () {
            var now = new Date();

        },
        changeStatus: function () {
            this.systemStatus = !this.systemStatus;
            $.getJSON('index.php?r=site/change-status', this.systemStatus);
        }
    }
});
log.init();