<!-- Google AdWords snippet -->
<script>
    window.addEventListener('load',function(){
        jQuery('[href="https://openstacksummit2018vancouver.eventbrite.com/"]').each(function(){
            var clientId = ga.getAll()[0].get('clientId');
            var theLink = jQuery(this).attr('href');
            jQuery(this).attr('href', theLink+ "?_eboga=" +clientId);
        });
    })
</script>
<!-- End Google AdWords snippet -->