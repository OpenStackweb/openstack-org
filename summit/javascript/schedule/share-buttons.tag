<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<share-buttons>

    <div class="facebook share_icon" onclick={shareFacebook}>
        <meta property="og:title" content="{this.share_info.title}" />
        <meta property="og:site_name" content="Openstack"/>
        <meta property="og:description" content="{this.share_info.description}" />
        <meta property="og:type" content="article" />
        <meta property="og:image" content="{this.share_info.image}" />
        <meta property="og:url" content="{this.share_info.url}" />
        <meta property="fb:app_id" content="{this.share_info.fb_app_id}" />
        <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
        </span>
    </div>
    <div class="twitter share_icon" onclick={shareTwitter}>
        <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
        </span>
    </div>
    <div class="email share_icon" onclick={shareMail}>
        <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-envelope fa-stack-1x fa-inverse"></i>
        </span>
    </div>

    <script>

        this.share_info   = opts.share_info;
        var self          = this;

        shareFacebook(e) {
            FB.ui({
                method: 'share',
                href: self.share_info.url,
            }, function(response){});
        }

        shareTwitter(e) {
            window.open('https://twitter.com/intent/tweet?text=Check out this summit event: '+self.share_info.url, 'mywin','left=50,top=50,width=600,height=260,toolbar=1,resizable=0');
            return false;
        }

        shareMail(e) {
            console.log("share mail");
        }

        window.fbAsyncInit = function() {
            FB.init({
                appId      : self.share_info.fb_app_id,
                xfbml      : true,
                version    : 'v2.5'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

    </script>


</share-buttons>