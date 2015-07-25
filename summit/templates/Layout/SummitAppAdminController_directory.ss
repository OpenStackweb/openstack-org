<div class="row">
        <div class="jumbotron">
            <h1>Summit Directory</h1>
        </div>
</div>
<div class ="row">
    <div class="col-md-12">
        <form class="form-inline" id="create_summit">
            <div class="form-group">
                <label for="summit-name">Summit Name</label>
                <input type="text" class="form-control" id="summit-name" name="summit_name" placeholder="Summit Name">
            </div>
            <div class="form-group">
                <label for="start-date">Start Date</label>
                <input type="text" class="form-control" id="start-date" name="start_date" placeholder="mm/dd/YYYY">
            </div>
            <div class="form-group">
                <label for="end-date">End Date</label>
                <input type="text" class="form-control" id="end-date" name="end_date" placeholder="mm/dd/YYYY">
            </div>
            <button type="submit" class="btn btn-lg btn-success">Create New Summit</button>
        </form>
    </div>
</div>
<div class ="row" style="padding-top: 2em;">
    <div class="col-md-12">
        <table class="table" id="summit_table">
            <tbody>
            <% loop Summits %>
                <tr id="summit_{$ID}">
                    <td class="summit_name">
                        $Title
                    </td>
                    <td>
                        $SummitBeginDate.Format('M jS Y')
                    </td>
                    <td>
                        $SummitEndDate.Format('M jS Y')
                    </td>
                    <td class="center_text">
                        <a href="$Top.Link/{$ID}/dashboard" class="btn btn-primary btn-sm" role="button">Control Panel</a>
                    </td>
                    <td class="center_text">
                        <a href="$Top.Link/{$ID}/edit" class="btn btn-default btn-sm" role="button">Edit</a>
                        <a href="#delete_summit_modal" data-toggle="modal" data-summit-id="{$ID}" class="btn btn-danger btn-sm delete_summit">Delete</a>
                    </td>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>
</div>
<div id="delete_summit_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>Are you sure you want to delete <span class="modal_summit_name"></span>?</p>
                <p class="text-warning"><small>All summit information will be lost.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" data-summit-id="" class="btn btn-danger modal_delete_btn">Delete Summit</button>
            </div>
        </div>
    </div>
</div>