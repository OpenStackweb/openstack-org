<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>
<feedback-form-comments>

    <feedback-form if={ showfeedbackform }  event="{ event }" dispatcher="{ dispatcher }"></feedback-form>
    <event-comments id="commentList" comments="{ comments }" event="{ event }" limit="{ limit }" dispatcher="{ dispatcher }"></event-comments>

    <script>
        this.comments          = opts.comments;
        this.showfeedbackform  = opts.showfeedbackform;
        this.event             = opts.event;
        this.limit             = opts.limit;
        this.dispatcher        = opts.dispatcher;
        var self               = this;
        var comments = [];

    </script>

</feedback-form-comments>

