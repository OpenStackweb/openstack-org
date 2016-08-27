<div class="" style="padding:20px">
    <h1>Review Summit</h1>
    <% if $Schedule %>
        <h4>Please rate the sessions listed below that you attended. If you did not attend one, just skip it. The list is
        based on the summit sessions you added to your personal schedule. Note: All feedback will be publicly viewable including your name.</h4>
        <br>
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
                        <td><a href="$getLink()" target="_blank">event details</a></td>
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
    <% else %>
        <div class="container">
            If you don't see any sessions to leave feedback on, you'll need to follow the instructions in the video below to
            link your EventBrite ID with your OpenStackID. From there, you can download the iOS or Android app (<a href="https://www.openstack.org/summit/austin-2016/mobile-apps/">here</a>)
            and select any session from the Austin Summit calendar, push the Feedback tab, and leave your rating and feedback.
            If you did not attend the OpenStack Summit Austin 2016, you will not be able to leave feedback at this time.
            <div style="text-align:center;margin-top:30px">
                <iframe width="640" height="390" src="http://www.youtube.com/embed/EQJb4smMSeQ"></iframe>
            </div>
        </div>
    <% end_if %>
</div>

<script type="text/javascript">
    var summit_id = {$Summit.ID};
</script>
