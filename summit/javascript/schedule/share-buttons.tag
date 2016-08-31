
<share-buttons>

    <div class="facebook share_icon" onclick={shareFacebook}>
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
                        <input type="hidden" id="email-token" value="{ share_info.token}" >
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
                href: self.share_info.event_url,
            }, function(response){});
        }

        shareTwitter(e) {
            window.open('https://twitter.com/intent/tweet?text=Check out this summit event: '+self.share_info.event_url, 'mywin','left=50,top=50,width=600,height=260,toolbar=1,resizable=0');
            return false;
        }

        shareMail(e) {
            console.log('email');
            $('#email-modal').modal('show');

            $('#email-form').validate();
        }

        sendEmail() {
            var url = 'api/v1/summits/current/schedule/'+self.share_info.event_id+'/share';
            var request = {
                from:$('#email-from').val(),
                to:$('#email-to').val(),
                token:$('#email-token').val()
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
                    swal('Email Sent', 'Email sent successfully', 'success');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                       var error = 'there was an issue with your request';
                       if( xhr.status == 412 ){
                             var response = $.parseJSON(xhr.responseText);
                             error        = response.messages[0].message;
                       }
                       $('#email-modal').modal('hide');
                       swal('Error', error, 'error');
                }
            });
        }

        window.fbAsyncInit = function() {
            FB.init({
                appId      : self.share_info.fb_app_id,
                xfbml      : true,
                status     : true,
                version    : 'v2.7'
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