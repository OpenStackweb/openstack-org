<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<event-comments>

    <div class="comment {last:parent.isLast(i)} {hidden:parent.isOutOfLimits(i)}" each={ comment, i in comments }>
        <div class="comment_info">
            <div class="comment_pic">
                <img src="{ comment.profile_pic }" alt="{comment.full_name}" />
            </div>
            <div class="comment_name"> {comment.full_name} </div>
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

    <a if={this.comments.length > this.limit} class="more_comments" onclick={ showMoreComments }> Show more comments </a>

    <script>

        this.comments          = opts.comments;
        this.limit             = opts.limit;
        var self               = this;


        isLast(i) {
            return ((self.limit - 1) == i || (self.comments.length - 1) == i);
        }

        isOutOfLimits(i) {
            return (i >= self.limit);
        }

        showMoreComments(e) {
            self.limit = self.limit + 5;
            return true;
        }

    </script>

</event-comments>