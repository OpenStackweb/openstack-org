<p>Thank you for registering with the OpenStack Foundation.</p>
<p>You will receive an email to verify your membership. If you do not receive an email verification, please send an email to <a href="mailto:support@openstack.org">support@openstack.org</a>.</p>
<p>you will now be redirected automatically to <a href="{$LoginUrl}">openstackid.org</a> on <span id="redirect-seconds-counter" style="font-weight: bold;">5</span> seconds.</p>
<script>
    var seconds = 5;
    window.setInterval(function(){
        seconds = seconds -1;
        $('#redirect-seconds-counter').text(seconds);
        if(seconds == 0)
            window.location = "{$LoginUrl}";
    }, 1000);
</script>