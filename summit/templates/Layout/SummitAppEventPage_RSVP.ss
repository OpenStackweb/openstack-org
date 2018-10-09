<script>
    var loadedFromAjaxRequest = false;
</script>
<div class="container-fluid">
    <div class="container section1">
       <div class="go-back">
        <a href="{$BackURL}" ><< Go back </a>
       </div>
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="title">$Event.Title</div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12 col-xs-12">
               $Event.Abstract
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12 col-rsvp">
                <% if HasRSVPAlready %>
                    <script>

                        swal({
                            title: "Done!",
                            text:"You already signed up for this event.",
                            type: "success"
                        }).then(function () {


                            var is_mobile              = bowser.mobile || bowser.tablet;
                            var is_ios                 = bowser.ios;
                            var is_android             = bowser.android;

                            if(is_android){
                                var form     = $('.rsvp_form');
                                var event_id = $('input[name="event_id"]', form).val();
                                window.location = "org.openstack.android.summit://events/"+event_id;
                            }
                            else {
                                var url = new URI(window.location);
                                if (url.hasQuery("BackURL")) {
                                    window.location = url.query(true)['BackURL'];
                                }
                            }
                        });

                    </script>

                <% else_if Event.RSVPTemplate.Exists()%>
                    <div class="container-fluid rsvp-container" >
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2" style="border: 1px solid darkblue;padding: 15px">
                                <h2><span class="label label-default">RSVP</span></h2>
                                $RSVPForm($Event.ID)
                            </div>
                        </div>
                    </div>
                <% end_if %>
            </div>
        </div>
    </div>
</div>