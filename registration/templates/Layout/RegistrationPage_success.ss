<p>Thank you for registering with the OpenStack Foundation.</p>
<p>You will receive an email to verify your membership. If you do not receive an email verification, please send an email to <a href="mailto:info@openstack.org">info@openstack.org</a>.</p>
<p>You will now be redirected automatically to <a href="{$LoginUrl}">openstackid.org</a> on <span id="redirect-seconds-counter" style="font-weight: bold;">5</span> seconds.</p>
<script>
    var seconds = 5;
    function countDown(){
        if(seconds > 0)
            seconds = seconds - 1;

        $('#redirect-seconds-counter').text(seconds);

        if(seconds == 0) {
            window.location = "{$LoginUrl}";
            return;
        }
        setTimeout(countDown,1000);
    }

    setTimeout(countDown, 1000);
</script>