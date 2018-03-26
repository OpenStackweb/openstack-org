<script type="text/javascript">

// Used to record outbound links before the browser resets to the new site

function recordOutboundLink(link, category, action) {
  try {
      ga('send', 'event', {
          eventCategory: category,
          eventAction: 'click',
          eventLabel: link.href
      });
      setTimeout('document.location = "' + link.href + '"', 100)
  }
  catch(err){}
}
</script>

<style>.async-hide { opacity: 0 !important} </style>
<script>
// Used to minimize flickering during GA A/B testing
  (function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
  h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
  (a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
  })(window,document.documentElement,'async-hide','dataLayer',4000,
  {'GTM-5F7R6N4':true});
</script>

<script>
// Google Analytics tracking script
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-17511903-1', 'auto');
    ga('require', 'GTM-5F7R6N4');
    ga('send', 'pageview');

</script>
