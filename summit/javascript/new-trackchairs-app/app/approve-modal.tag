<!-- Modal -->
<approve-modal>
  <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Review Category Change</h4>
        </div>
        <div class="modal-body">

          Click Approve Change to move the presentation <strong>{ opts.request.presentation_title }</strong> to the category "<strong>{ opts.request.new_category.title }</strong>".

        </div>

        <div class="modal-footer" show="{ !finished }">
          <button type="button" class="btn btn-default" onclick="{ showPresentation(opts.request.presentation_id) }" data-dismiss="modal">See Presentation</button>
          <button type="button" class="btn btn-primary" onclick="{ approveChange(opts.request) }" data-dismiss="modal">Approve Change</button>
        </div>

      </div>
    </div>
  </div>

  <script>

  var self = this

  showPresentation(presId) {
    return function(e) {
      riot.route('presentations/show/' + presId)
    }
  }

  approveChange(request) {
    return function(e) {
      self.parent.parent.opts.trigger('approve-change', request.id)
    }
  }



  </script>

</approve-modal>