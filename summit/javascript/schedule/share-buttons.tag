
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

    <div id="email-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Email</h4>
                </div>
                <div class="modal-body">
                    <form id="email-form">
                        <div class="form-group">
                            <label for="email-from">From:</label>
                            <input type="email" class="form-control" id="email-from" required>
                        </div>
                        <div class="form-group">
                            <label for="email-to">To:</label>
                            <input type="email" class="form-control" id="email-to" required>
                        </div>
                        <div class="form-group">
                            <label for="email-subject">Subject:</label>
                            <input type="text" class="form-control" id="email-subject" value="Fwd: { share_info.title}" >
                        </div>
                        <div class="form-group">
                            <label for="email-body">Body:</label>
                            <textarea id="email-body" class="form-control">
                                { share_info.title }
                                &#13;&#10;
                                { share_info.description }
                                &#13;&#10;&#13;&#10;
                                Check it out: { share_info.url }
                            </textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick={ sendEmail }>Send</button>
                </div>
            </div>
        </div>
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
            console.log('email');
            $('#email-modal').modal('show');

            $('#email-form').validate();
        }

        sendEmail() {
            var url = 'api/v1/summits/6/schedule/shareEmail';
            var request = {
                from:$('#email-from').val(),
                to:$('#email-to').val(),
                subject:$('#email-subject').val(),
                body:$('#email-body').val()
            }

            if (!$('#email-form').valid()) {
                return false;
            }

            $.ajax({
                type: 'POST',
                url:  url,
                data: JSON.stringify(request),
                contentType: "application/json; charset=utf-8",
                success: function () {
                    $('#email-modal').modal('hide');
                    swal('Success');
                }
            });
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