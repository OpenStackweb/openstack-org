<div class="row">
    <div class="col-md-8">
        <h2>User Stories List</h2>
    </div>
    <div class="col-md-4">
        <a href="/sangria/user-stories/new" class="btn btn-default pull-right">New Story</a>
    </div>
</div>

<% if UserStories %>
    <table id="user-stories-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Short Desc</th>
                <th>Link</th>
                <th>Industry</th>
                <th>Location</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <% loop UserStories %>
            <tr>
                <td>$Name</td>
                <td>$ShortDescription</td>
                <td>$Link</td>
                <td>$Industry.IndustryName</td>
                <td>$Location.Name</td>
                <td width="5%">
                    <a href="sangria/user-stories/edit/$ID" id="edit-story" class="action-story" title="edit story">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    <a id="delete-story" class="action-story" data-href="sangria/user-stories/delete/{$ID}" data-toggle="modal" data-target="#confirm-delete-story" title="delete story">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
        <% end_loop %>
        </tbody>
    </table>
<% else %>
    <p>* There are not any User Stories yet.</p>
<% end_if %>

<!-- Modal -->
<div id="confirm-delete-story" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Story</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user story?</p>
            </div>
            <div class="modal-footer">
                <a href="" type="button" class="btn btn-danger pull-left btn-delete" >Delete</a>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>