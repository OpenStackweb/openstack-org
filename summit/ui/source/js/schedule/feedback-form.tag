<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>
<feedback-form>

    <div class="feedback_box">
        <div class="container">
            <div>Rating</div>
            <input id="rating"  class="rating" min="1" max="5" data-size="xs" >
            <div>Comment</div>
            <textarea id="comment"></textarea>
            <input id="event_id"  value="{event.eventID}" type="hidden" />
            <input id="summit_id" value="{event.summitID}" type="hidden" />
            <input id="member_id" value="{event.memberID}" type="hidden" />
            <div>
                <button onclick={ submitEventFeedback } id="btn_submit_event_feedback" type="button" class="btn btn-primary btn-md active btn-warning save">Submit</button>
            </div>
        </div>
    </div>

    <script>
        var self               = this;
        this.event             = opts.event;
        this.dispatcher        = opts.dispatcher;

        submitEventFeedback(e) {

                var event_id  = $('#event_id').val();
                var rating    = $('#rating').val();
                var comment   = $('#comment').val();
                var member_id = $('#member_id').val();
                var summit_id = $('#summit_id').val();
                var feedback  = {rating: rating, comment: comment, member_id: member_id, event_id: event_id, Approved : 1};

                if (rating == 0 ){
                    swal('Error', 'Please fill in the rating.', 'warning');
                    return;
                }

                $('#'+event_id).prop('disabled',true);

                $.ajax({
                    type: 'POST',
                    url:  'api/v1/summits/'+summit_id+'/schedule/'+event_id+'/feedback',
                    data: JSON.stringify(feedback),
                    timeout:10000,
                    contentType: "application/json; charset=utf-8",
                    success: function (data) {
                        swal({
                            title: 'Thanks!',
                            text: 'Your feedback has been sent!',
                            type: "success",
                        }).then(function () {
                            $('.feedback_box').hide();
                            var comment = { rate:  feedback.rating, rate : "1 second ago", note : feedback.comment};
                            self.dispatcher.submitFeedback(comment);
                        });
                    }

                }).fail(function (jqXHR, textStatus, errorThrown) {
                    var http_code = jqXHR.status;

                    if(http_code === 401){
                        swal({title:'Error', text:'you are not logged in!', type: 'error'});
                        location.reload();
                    }
                    if(http_code === 412){
                        var response  = jqXHR.responseJSON;
                        swal({
                            title: response.error,
                            text : response.messages[0].message,
                            type: "warning",
                        });
                    }
                    if(http_code === 404){
                        swal({title:'Error', text:'Event not found', type: 'error'});
                    }
                });
        }

        this.on('mount', function(){
            $(".rating").rating({showCaption:false, showClear:false, step: 1 , size: "xxxs"});
        });
    </script>

</feedback-form>

