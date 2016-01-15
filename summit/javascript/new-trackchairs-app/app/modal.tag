<!-- Modal -->
<modal>
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Suggest a category change</h4>
        </div>
        <div class="modal-body">

          <div show="{ !finished }">
            <p>I would like to suggest that this presentation:</p>
            <p><strong>{ opts.presentation.title }</strong></p>

            <p>Be moved to this category:
            <select name="catMoveSelector">
              <option each="{ category in opts.categories }" value="{category.id}">{category.title}</option>
            </select></p>

          </div>

          <div show="{ finished }">
              <p>Ok, thanks! We will notify the chairs of the other category that you would like to make the switch. If the one of the chairs of the other category agrees to the change, the presentation will be moved.</p>
          </div>

        </div>
        <div class="modal-footer" show="{ !finished }">
          <button type="button" class="btn btn-default" data-dismiss="modal" onclick="{ closeModal }">Close</button>
          <button type="button" class="btn btn-primary" onclick="{ suggestChange }">Suggest Change</button>
        </div>
        <div class="modal-footer" show="{ finished }">
          <button type="button" class="btn btn-primary" onclick="{ closeModal }" data-dismiss="modal">OK</button>
        </div>        
      </div>
    </div>
  </div>

  <script>

    var self = this

    this.on('mount', function(){
      this.finished = false
    })

    suggestChange(e) {

      var change = {}
      change.new_category = this.catMoveSelector.value
      change.presentation_id = this.opts.presentation.id

      opts.api.trigger('suggest-category-change', change)

    }

    closeModal() {
      this.finished = false
    }

    this.opts.api.on('category-change-suggested', function(){
        self.finished = true
        self.update()
    })


  </script>

</modal>