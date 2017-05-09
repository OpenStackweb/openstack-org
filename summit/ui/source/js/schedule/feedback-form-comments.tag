<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>
<feedback-form-comments>

    <feedback-form if={ current_user != null && event.has_ended && event.allow_feedback && !current_user.has_feedback } current_user={ current_user } event="{ event }" dispatcher="{ dispatcher }"></feedback-form>
    <event-comments if={ event.has_ended } id="commentList" event="{ event }" limit="{ limit }" dispatcher="{ dispatcher }"></event-comments>

    <script>
        this.event             = opts.event;
        this.limit             = opts.limit;
        this.current_user      = opts.current_user;
        this.dispatcher        = opts.dispatcher;
        var self               = this;

    </script>

</feedback-form-comments>

