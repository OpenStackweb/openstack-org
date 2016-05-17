<div class="" style="padding:20px">
    <h1>Review Summit</h1>


    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Link</th>
                <th>Speakers</th>
                <th>Rate</th>
                <th>Comment</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <% loop $Schedule %>
                <tr class="event">
                    <td>$Title</td>
                    <td><a href="$Link">event details</a></td>
                    <td>
                    <% loop $Speakers %>
                        $FirstName $LastName <% if $Last %> <% else %> , <% end_if %>
                    <% end_loop %>
                    </td>
                    <td>
                        <input id="rating-$ID" data-event-id="$ID" class="rating" value="$CurrentMemberFeedback.Rate"/>
                    </td>
                    <td style="width:500px">
                        <textarea class="comment" id="comment-$ID" data-event-id="$ID" style="width:100%;height:100px;"> $CurrentMemberFeedback.Note </textarea>
                    </td>
                    <td>
                        <button id="$ID" type="button" class="btn btn-success save">Save</button>
                    </td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    var summit_id = {$Summit.ID};
</script>
