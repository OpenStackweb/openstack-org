<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>
<event-comments>

    <div id="feedbackContainer" class="container">
        <div class="col1 comment_section">
            <div class="comment_title"> Comments </div>
            <div class="comment" >
                <div><span id="feedbackCount">{ event.comments.length}</span> Reviews</div>
                <div style="display:inline">
                    <div style="float:left"><input id="rating" class="avgRating" disabled value="{ event.avg_rate }" min="1" max="5" ></div>
                    <div style="float:left"><span id="avgRate"> { event.avg_rate }</span></div>
                </div>
            </div>


            <div class="comment {last:parent.isLast(i)} {hidden:parent.isOutOfLimits(i)}" each={ comment, i in event.comments }>
                <div class="comment_info">
                    <div>
                        <input id="rating"  class="rating" readOnly value="{comment.rate}" min="1" max="5" >
                    </div>
                    <div class="comment_date">
                        <b> Posted: </b>
                        <span> {comment.date} </span>
                    </div>
                </div>
                <div class="comment_text"> {comment.note} </div>
                <div class="comment_actions">
                    <div class=""></div>
                </div>
            </div>

            <a if={this.event.comments.length > this.limit} class="more_comments" onclick={ showMoreComments }> Show more comments </a>

            <script>

                this.event             = opts.event;
                this.limit             = opts.limit;
                this.dispatcher        = opts.dispatcher;
                var self               = this;

                isLast(i) {
                    return ((self.limit - 1) == i || (self.event.comments.length - 1) == i);
                }

                isOutOfLimits(i) {
                    return (i >= self.limit);
                }

                showMoreComments(e) {
                    self.limit = self.limit + 5;
                    return true;
                }

                self.dispatcher.on(self.dispatcher.SUBMIT_FEEDBACK,function(comment){
                    self.event.comments.unshift(comment);
                    self.update();
                    $(".rating").rating({showCaption:false,showClear:false,step:0.5, size: "xxxs"});
                });

                this.on('mount', function(){
                    $(".rating").rating({showCaption:false,showClear:false,step:0.5, size: "xxxs"});
                    $(".avgRating").rating({showCaption:false, showClear:false, step:0.1, size: "xxxs"});
                });
            </script>
        </div>
    </div>

</event-comments>

