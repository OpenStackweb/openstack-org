<% if Message %>
    <div class="alert alert-{$MessageType} alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>$Message</p>
	</div>
<% end_if %>