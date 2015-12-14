<div class="modal fade" id="modal-edit-affiliation" tabindex="-1" role="dialog" aria-labelledby="editAffiliationLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editAffiliationLabel">{$Title}}</h4>
            </div>
            <div class="modal-body">
                <form id="edit-affiliation-form" name="edit-affiliation-form">
                    <div class="form-group">
                        <input type="text" class="form-control" id="OrgName" name="OrgName" placeholder="Organization">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="StartDate" name="StartDate" placeholder="Start Date">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="EndDate" name="EndDate" placeholder="End Date">
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="Current" name="Current">&nbsp;Is Current?
                        </label>
                    </div>
                    <input type="hidden" id="Id" name="Id" value="0"/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="btn-save-affiliation" name="btn-save-affiliation" type="button" class="btn btn-primary btn-save-affiliation">Save changes</button>
            </div>
        </div>
    </div>
</div>