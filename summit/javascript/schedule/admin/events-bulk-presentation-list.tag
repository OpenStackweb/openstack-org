<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<events-bulk-presentation-list>

    <div class="panel panel-default">
        <div class="panel-heading">Unpublished Presentations ({ page_data.total })</div>

        <table class="table">
            <thead>
                <tr>
                    <th width="10%">ID</th>
                    <th>Presentation</th>
                    <th width="20%">Speakers</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ presentation, i in presentations } data-id="{ presentation.id }">
                    <td>
                        <a href="/summit-admin/{ summit_id }/events/{ presentation.id }" target="_blank">
                            { presentation.id }
                        </a>
                    </td>
                    <td>
                        <input style="width:100%" type="text" class="title" value={ presentation.title } />
                    </td>
                    <td>
                        <span each={ speaker, i in presentation.speakers }>{ speaker.name }, </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <nav>
    <ul id="list-pager" class="pagination"></ul>
    </nav>

    <script>
        this.dispatcher      = opts.dispatcher;
        this.page_data       = {total: 100, limit: opts.page_limit, page: 1};
        this.summit_id       = opts.summit_id;
        this.presentations   = [];
        var self             = this;


        this.on('mount', function() {
            self.getList(1);

            $('.reports-wrapper').on('change','input',function(){
                $(this).parents('tr').addClass('changed');
            });
        });

        getList(page) {
            $('body').ajax_loader();
            var term = $('#search-term').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/events/unpublished/presentations',
                {page:page, page_size: self.page_data.limit, search_term: term, expand: 'speakers'},
                function(data){
                    self.presentations = data.data;
                    self.page_data.page = page;
                    self.page_data.total = parseInt(data.total);

                    var total_pages = (self.page_data.total) ? Math.ceil(self.page_data.total / self.page_data.limit) : 1;
                    var options = {
                        bootstrapMajorVersion:3,
                        currentPage: self.page_data.page ,
                        totalPages: total_pages,
                        numberOfPages: 10,
                        onPageChanged: function(e,oldPage,newPage){
                            self.parent.saveList();
                            self.getList(newPage);
                        }
                    }

                    $('#list-pager').bootstrapPaginator(options);

                    self.update();
                    $('body').ajax_loader('stop');
            });
        }

        self.dispatcher.on(self.dispatcher.SAVE_PRESENTATIONS,function(report) {
            var request = [];
            $('.changed').each(function(){
                var presentation_id = $(this).data('id');
                var title           = $('.title',this).val();
                request.push({id: presentation_id, title: title});
            });

            if (request.length) {
                $.ajax({
                    type: 'PUT',
                    url: 'api/v1/summits/'+self.summit_id+'/events/bulk_presentations',
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
        });

        self.dispatcher.on(self.dispatcher.GET_PRESENTATIONS,function() {
            self.getList(1);
        });

    </script>

</events-bulk-presentation-list>