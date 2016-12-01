<schedule-admin-view-published-bulk-actions>
    <div class="row bulk-actions-container">
        <div class="col-md-6">
            <input disabled="disabled" type="checkbox" id="select_all_published" title="select all"/>
            <select disabled="disabled" id="bulk-actions-published" name="bulk-actions-published">
                <option value="">-- Select a Bulk Action --</option>
                <option value="EDIT">Edit</option>
                <option value="UNPUBLISH">Unpublish</option>
            </select>
        </div>
        <div class="col-md-6">
            <button title="Apply Bulk Action" disabled="disabled" class="btn btn-default btn-sm" id="apply-bulk-published-action"><i class="fa fa-play">&nbsp;GO</i></button>
        </div>
    </div>

    <script>

    this.bulk_edition_url = opts.bulk_edition_url;
    this.published_store  = opts.published_store;
    this.summit           = opts.summit;
    var self              = this;

    self.published_store.on(self.published_store.LOAD_STORE,function() {
        if(self.published_store.isEmpty())
        {
            $('#select_all_published').attr('disabled', 'disabled');
        }
        else
        {
            $('#select_all_published').removeAttr('disabled');
        }
        $('#select_all_published').prop('checked', false);
        $('#bulk-actions-published').val('');
        $('#apply-bulk-published-action').attr('disabled', 'disabled');
        $('#bulk-actions-published').attr('disabled', 'disabled');
    });

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
                            function(isConfirm){
                                if (isConfirm) {
                                    $('body').ajax_loader();
                                    $.ajax({
                                        type: 'DELETE',
                                        url: 'api/v1/summits/'+self.summit.id+'/events/unpublish/bulk',
                                        data: JSON.stringify(ids),
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json"
                                    }).done(function(saved_event) {
                                        $('body').ajax_loader('stop');
                                        swal("Updated!", "Events were unpublished successfully.", "success");
                                        location.reload();
                                    }).fail(function(jqXHR) {
                                        $('body').ajax_loader('stop');
                                        var responseCode = jqXHR.status;
                                        if(responseCode == 412) {
                                        var response = $.parseJSON(jqXHR.responseText);
                                            swal('Validation error', response.messages[0].message, 'warning');
                                            return;
                                        }
                                        swal('Error', 'There was a problem saving the events, please contact admin.', 'warning');
                                    });
                                }
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