<script>
    $(document).ready(function() {
        /// keep session alive
        var refreshSn = function() {
            var time = 600000; // 10 minutes
            //var time = 1000; // 1 sec
            setTimeout(function() {
                $.ajax({
                    method: 'POST',
                    url: '/keepalive',
                    cache: false,
                    complete: function() {
                        refreshSn();
                    }
                });
            }, time);
        };
        refreshSn();

    });
</script>