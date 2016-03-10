<schedule-admin-view-published-bulk-actions>
    <div class="row bulk-actions-container">
        <div class="col-md-4">
            <input type="checkbox" id="select_all_published" title="select all"/>
            <select disabled="disabled" id="bulk-actions-published" name="bulk-actions-published">
                <option value="">-- Select a Bulk Action --</option>
                <option value="EDIT">Edit</option>
                <option value="UNPUBLISH">Unpublish</option>
            </select>
        </div>
        <div class="col-md-8">
            <button title="Apply Bulk Action" disabled="disabled" class="btn btn-default btn-sm" id="apply-bulk-published-action"><i class="fa fa-play">&nbsp;GO</i></button>
        </div>
    </div>

    <script>
    this.bulk_edition_url = opts.bulk_edition_url;
    var self              = this;

    this.on('mount', function(){
        $(function() {
            $('body').on('click', '.bulk-action-published-selector', function() {
                var enable = $(".bulk-action-published-selector:checked").length > 0;
                if(enable)
                {
                    $('#apply-bulk-published-action').removeAttr('disabled');
                    $('#bulk-actions-published').removeAttr('disabled');
                }
                else
                {
                    $('#bulk-actions-published').val('');
                    $('#apply-bulk-published-action').attr('disabled', 'disabled');
                    $('#bulk-actions-published').attr('disabled', 'disabled');
                }
            });

            $('#select_all_published').click(function(){
                var all_checked = $(this).is(':checked');
                $(".bulk-action-published-selector").prop('checked', all_checked);
                if(all_checked)
                {
                    $('#apply-bulk-published-action').removeAttr('disabled');
                    $('#bulk-actions-published').removeAttr('disabled');
                }
                else
                {
                    $('#bulk-actions-published').val('');
                    $('#apply-bulk-published-action').attr('disabled', 'disabled');
                    $('#bulk-actions-published').attr('disabled', 'disabled');
                }
            });

            $('#apply-bulk-published-action').click(function(evt){
                var selected_events = $(".bulk-action-published-selector:checked");
                var ids = [];
                selected_events.each(function(){
                    var id = $(this).attr('data-event-id');
                    ids.push(parseInt(id));
                });
                var action = $('#bulk-actions-published').val();

                switch(action){
                    case 'EDIT':
                    {
                        window.location = self.bulk_edition_url+'?action=edit&type=published&event_ids='+ids.join();
                    }
                    break;
                    case 'UNPUBLISH':
                    {
                        swal({
                            title: "Are you sure?",
                            text: "You about to unpublish these events from summit live schedule",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, Unpublish them all!",
                            closeOnConfirm: true },
                            function(){
                            }
                        );
                    }
                    break;
                }
                return false;
            });
        });
    });

    </script>
</schedule-admin-view-published-bulk-actions>