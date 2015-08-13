<!-- Modal -->
<change-error-modal>
  <div class="modal fade" id="changeErrorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Oops...</h4>
        </div>
        <div class="modal-body">

          There was a request made to move this presentation to <strong>{ opts.request.new_category.title }</strong>, but it has already been selected by the track chairs of the current category, <strong>{ opts.request.old_category.title }</strong>. In order to move it, you'll need to ask if the chairs if they will unselect the presentation first.

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
        </div>

      </div>
    </div>
  </div>

</change-error-modal>