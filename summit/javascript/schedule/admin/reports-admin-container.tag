<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-container>
    <div class="row">
        <div class="col-md-4">
            <select id="report_select" class="form-control" >
                <option value="speaker_report"> Speaker Report </option>
                <option value="presentation_report" selected> Presentation Report </option>
                <option value="room_report"> Speakers Per Room </option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary" id="export-report" onclick={ exportReport } >Export CSV</button>
            <button class="btn btn-success" id="save-report" onclick={ saveReport } >Save</button>
        </div>
    </div>
    <br>

    <reports-admin-speaker-report if={report == 'speaker_report'} page_limit="{ limit }" summit_id="{ summit_id }"></reports-admin-speaker-report>
    <reports-admin-presentation-report if={report == 'presentation_report'} page_limit="{ limit }" summit_id="{ summit_id }"></reports-admin-presentation-report>
    <reports-admin-room-report if={report == 'room_report'} summit_id="{ summit_id }"></reports-admin-room-report>

    <script>
        this.report     = opts.report;
        this.summit_id  = opts.summit_id;
        this.limit      = opts.limit;
        var self        = this;

        this.on('mount', function() {
            $("#report_select").change(function(){
                self.report = $(this).val();
                self.update();
            });

        });

        saveReport() {
            var request = [];
            $('.changed').each(function(){
                var id = $(this).data('id');
                var phone = $('.phone',this).val();
                var registered = $('.registered',this).attr('checked') ? 1 : 0;
                var checked_in = $('.checked_in',this).attr('checked') ? 1 : 0;

                request.push({id: id, phone: phone, registered: registered, checked_in: checked_in});
            });

            if (request.length) {
                $.ajax({
                    type: 'PUT',
                    url: 'api/v1/summits/'+self.summit_id+'/reports/save_report',
                    data: JSON.stringify(request),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json"
                    }).done(function(data) {
                        $('.changed').removeClass('changed');
                        swal('Updated', 'Changes saved.', 'success');
                    }).fail(function(jqXHR) {
                        var responseCode = jqXHR.status;
                        if(responseCode == 412) {
                            var response = $.parseJSON(jqXHR.responseText);
                            swal('Validation error', response.messages[0].message, 'warning');
                        } else {
                            swal('Error', 'There was a problem saving the speaker, please contact admin.', 'warning');
                        }
                });
            }
        }

        exportReport(sort,sort_dir) {
            var report = $('#report_select').val();
            var sort = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');
            $('body').ajax_loader();

            $.get('api/v1/summits/'+self.summit_id+'/reports/export/'+report,
                {sort:sort, sort_dir:sort_dir},
                function(csv){
                    $('body').ajax_loader('stop');
                    var uri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
                    var download_link = document.createElement('a');
                    download_link.href = uri;
                    download_link.download = report+".csv";
                    document.body.appendChild(download_link);
                    download_link.click();
                    document.body.removeChild(download_link);
            });
        }

        toggleSort(elem) {
            var sort_dir = (elem.data('dir') == 'ASC') ? 'DESC' : 'ASC';
            elem.data('dir',sort_dir);
            $('.fa','.sortable').remove();
            $('.sorted').removeClass('sorted');

            var arrow = (sort_dir == 'ASC') ? '<i class="fa fa-caret-up"></i>' : '<i class="fa fa-caret-down"></i>';
            elem.html(elem.text()+arrow);
            elem.addClass('sorted');
        }

    </script>

</reports-admin-container>