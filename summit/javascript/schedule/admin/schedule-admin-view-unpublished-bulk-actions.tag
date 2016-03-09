<schedule-admin-view-unpublished-bulk-actions>
    <div class="row">
        <div class="col-md-4">
            <select disabled="disabled" id="bulk-actions-unpublished" name="bulk-actions-unpublished" class="bulk-actions">
                <option value="">--Select a Bulk Action --</option>
                <option value="EDIT">Edit</option>
                <option value="PUBLISH">Publish</option>
            </select>
        </div>
        <div class="col-md-8">
            <button disabled="disabled" class="btn btn-default btn-sm" id="apply-bulk-unpublished-action"><i class="fa fa-play">&nbsp;GO</i></button>
        </div>
    </div>

    <script>
    this.bulk_edition_url = opts.bulk_edition_url;
    var self              = this;

    this.on('mount', function(){
        $(function() {
            $('body').on('click', '.bulk-action-unpublished-selector', function() {
                var enable = $(".bulk-action-unpublished-selector:checked").length > 0;
                if(enable)
                {
                    $('#apply-bulk-unpublished-action').removeAttr('disabled');
                    $('#bulk-actions-unpublished').removeAttr('disabled');
                }
                else
                {
                    $('#bulk-actions-unpublished').val('');
                    $('#apply-bulk-unpublished-action').attr('disabled', 'disabled');
                    $('#bulk-actions-unpublished').attr('disabled', 'disabled');
                }
            });

            $('#apply-bulk-unpublished-action').click(function(evt){
                var selected_events = $(".bulk-action-unpublished-selector:checked");
                var ids = [];
                selected_events.each(function(){
                    var id = $(this).attr('data-event-id');
                    ids.push(parseInt(id));
                });
                var action = $('#bulk-actions-unpublished').val();
                switch(action){
                    case 'EDIT':
                    {
                        window.location = self.bulk_edition_url+'?action=edit&type=unpublished&event_ids='+ids.join();
                    }
                    break;
                    case 'PUBLISH':
                    {
                        window.location = self.bulk_edition_url+'?action=publish&type=unpublished&event_ids='+ids.join();
                    }
                    break;
                }
                return false;
            });
        });
    });

    </script>
</schedule-admin-view-unpublished-bulk-actions>