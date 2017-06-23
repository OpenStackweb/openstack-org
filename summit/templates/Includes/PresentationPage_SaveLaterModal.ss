<div class="modal fade" id="save-later-modal" tabindex="-1" role="dialog" aria-labelledby="save-later-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: 105%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><strong>Save & Come Back Later</strong></h4>
            </div>
            <div class="modal-body">
                <p>Your progress has been saved! Come back by going to the url below. <strong>See you soon!</strong></p>
                <input type="text" class="form-control" readonly value="{$StepUrl}" id="txt_url">
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-center">
                    <button id="btn-copy-url" type="button" data-clipboard-target="#txt_url" class="btn btn-primary">
                        COPY URL&nbsp&nbsp;<i class="fa fa-clipboard" aria-hidden="true"></i>
                    </button>
                    <script>
                        new Clipboard('#btn-copy-url');
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>