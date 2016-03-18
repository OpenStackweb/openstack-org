<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<speakers-list>
    <div class="row">
        <div class="col-md-6" style="margin:0  0 20px 0;">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="speakers_search_term" class="form-control input-global-search" placeholder="Search by Name">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" id="search_speakers"><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick="{ clearClicked }">
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

    <div class="panel panel-default" if={ page_data.total_items > 0 }>
        <div class="panel-heading">Speakers ({ page_data.total_items })</div>

        <table id="speakers-table" class="table">
            <thead>
                <tr>
                    <th><a title="sort by Speaker Id" style="cursor:pointer;" onclick="{ sortBy }" data-field='id' data-dir='asc'>Id</a></th>
                    <th><a title="sort by Speaker FullName" style="cursor:pointer;" onclick="{ sortBy }" data-field='fullname' data-dir='asc'>FullName</a></th>
                    <th><a title="sort by Email" style="cursor:pointer;" onclick="{ sortBy }" data-field='email' data-dir='asc'>Email</a></th>
                    <th>Member Id</th>
                    <th>Summit On Site Phone</th>
                    <th># Presentations</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ speaker, i in speakers }>
                    <td>{ speaker.id }</td>
                    <td>{ speaker.name }</td>
                    <td>{ speaker.email }</td>
                    <td>{ speaker.member_id }</td>
                    <td>{ speaker.onsite_phone }</td>
                    <td>{ speaker.presentation_count }</td>
                    <td><a href="{ parent.edit_link+'/'+speaker.id }" class="btn btn-default btn-sm active" role="button">Edit</a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="panel panel-default" if={ page_data.total_items == 0 }>
        <span class="no_speakers_msg"> No Speakers found.</span>
    </div>

    <nav>
    <ul id="speakers-pager" class="pagination"></ul>
    </nav>

        <script>

        this.speakers      = opts.speakers;
        this.page_data     = opts.page_data;
        this.summit_id     = opts.summit_id;
        this.edit_link     = opts.edit_link;
        this.total_pages   = 0;
        this.sort_by       = 'id';
        this.sort_dir      = 'asc';
        var self           = this;

        this.on('mount', function() {

            self.total_pages = self.page_data.total_items > 0 ? Math.ceil(self.page_data.total_items / self.page_data.limit): 0;
            var options = {
                bootstrapMajorVersion:3,
                currentPage: self.page_data.page ,
                totalPages: self.total_pages ,
                numberOfPages: 10,
                onPageChanged: function(e,oldPage,newPage){
                    self.getSpeakers(newPage, $('#speakers_search_term').val());
                }
            };

            $('#speakers-pager').bootstrapPaginator(options);

            $('#search_speakers').click(function(e) {
                var search_term = $('#speakers_search_term').val();
                self.getSpeakers(1, search_term);
            });

            $("#speakers_search_term").keydown(function (e) {
                    if (e.keyCode == 13) {
                        $('#search_speakers').click();
                    }
            });
        });

        getSpeakers(page, search_term) {

                $('body').ajax_loader();

                $.getJSON('api/v1/summits/'+self.summit_id+'/speakers',{
                        page:page,
                        items:self.page_data.limit,
                        term: search_term,
                        sort_by: self.sort_by,
                        sort_dir: self.sort_dir,
                    },function(data){

                    self.speakers              = data.speakers;
                    self.page_data.page        = data.page;
                    self.page_data.total_items = data.count;
                    self.total_pages           = self.page_data.total_items > 0 ? Math.ceil(self.page_data.total_items / self.page_data.limit): 0;
                    if(self.speakers .length > 0){
                        var options = {
                            bootstrapMajorVersion:3,
                            currentPage: self.page_data.page ,
                            totalPages: self.total_pages,
                            numberOfPages: 10,
                            onPageChanged: function(e,oldPage,newPage){
                                self.getSpeakers(newPage, $('#speakers_search_term').val());
                            }
                        };

                        $('#speakers-pager').bootstrapPaginator(options);
                        $('#speakers-pager').show();
                    }
                    else $('#speakers-pager').hide();
                    self.update();
                    $('body').ajax_loader('stop');
                });
        }

        sortBy(e) {
            var target = $(e.currentTarget);
            self.sort_by= target.attr('data-field');
            self.sort_dir= target.attr('data-dir');
            target.attr('data-dir', self.sort_dir === 'asc' ? 'desc' : 'asc');
            self.getSpeakers(1, $('#speakers_search_term').val().trim());
        }

        clearClicked(e){
            $('#speakers_search_term').val('');
            self.getSpeakers(1,null);
        }

        </script>
</speakers-list>