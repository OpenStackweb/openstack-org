<!-- Modal -->
<div class="modal fade" id="ModalSaveLater" tabindex="-1" role="dialog" aria-labelledby="ModalSaveLaterLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ModalSaveLaterLabel"><strong>Save & Come Back Later</strong></h4>
            </div>
            <div class="modal-body">
                <p>Your progress has been saved! Come back by going to the url below. <strong>See you soon!</strong></p>
                <input type="text" readonly value="" id="txt_url">
            </div>
            <div class="modal-footer">
                <p>
                    <button id="btn-copy-url" type="button" data-clipboard-target="#txt_url" class="btn btn-primary">COPY URL&nbsp;<i class="fa fa-clipboard" aria-hidden="true"></i></button>
                    <script>
                        $('#txt_url').val(window.location);
                        new Clipboard('#btn-copy-url');
                    </script>
                </p>
            </div>
        </div>
    </div>
</div>