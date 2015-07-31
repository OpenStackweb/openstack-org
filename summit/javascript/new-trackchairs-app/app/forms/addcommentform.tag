<addcommentform>

	<form onsubmit="{addComment}">
		<textarea id="commentBody" style="width: 100%;"></textarea><br/>
		<button type="button" class="btn btn-default" onclick="{addComment}">Add Comment</button>
	</form>

	<script>

		addComment() {
			opts.api.trigger('add-comment',opts.presentation.id,commentBody.value)

			// Setup the comments array if no comments have yet been posted
			if (!opts.presentation.comments) opts.presentation.comments = new Array()

			opts.presentation.comments.push({ body: commentBody.value })

			// Clear the form field
			commentBody.value = ''
			this.parent.update()
		}
	</script>

</addcommentform>