<comments-list>

  <h1>Comments List</h1>
  <p>(Sorted with most recent comments at the top.)</p>

  <div each="{comment in comments}" class="comment" onclick="{ showPresentation(comment.presentaiton_id) }">
    <div class="comment-body {system-comment: comment.system_comment }">
      { comment.body }
      <div class="presentation-line">Comment made on <strong>{ comment.presentaiton_title }</strong> <span if="{ comment.commenter != ' '}">by { comment.commenter }</span>.</div>
    </div>
  </div>

  <style scoped>
		.comment-body {border: 1px solid #CBCBCB; padding: 15px; margin-bottom: 10px;}
    .presentation-line { border-top: 1px solid #CBCBCB; margin-top: 10px; padding-top: 10px; font-size: 80%;}
    .system-comment {background-color: #E7E7E7; color: #707070; }
	</style>

  <script>

		var self = this
		self.comments = []

		this.on('mount', function(){
			opts.api.trigger('load-all-comments')
		})

		opts.api.on('all-comments-loaded', function(response){
			self.comments = []
			self.update()
			self.comments = response.results
			self.update()
		})

    showPresentation(presId) {
      return function(e) {
        riot.route('presentations/show/' + presId)
      }
    }

	</script>

</comments-list>
