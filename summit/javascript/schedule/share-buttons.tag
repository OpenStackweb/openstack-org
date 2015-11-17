<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

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

    <script>

        this.url          = opts.url;
        var self          = this;

        shareFacebook(e) {
            console.log("share fb");
        }

        shareTwitter(e) {
            console.log("share tw");
        }

        shareMail(e) {
            console.log("share mail");
        }

    </script>

</share-buttons>