<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<promocode-sponsor-list>
    <div class="row">
        <div class="col-md-6" style="margin:0  0 20px 0;">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="promocode_search_term" class="form-control input-global-search" placeholder="Search by Code, Speaker, Member, Email, Company">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" id="search_promocode"><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick="{ clearClicked }">
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

    <div class="panel panel-default" if={ page_data.total_items > 0 }>
        <div class="panel-heading">Promo Codes ({ page_data.total_items })</div>

        <table id="promocode-table" class="table">
            <thead>
                <tr>
                    <th>Sponsor ID</th>
                    <th><a title="sort by Sponsor" style="cursor:pointer;" onclick="{ sortBy }" data-field='name' data-dir='asc'>Sponsor</a></th>
                    <th>Codes</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ pcode, i in promo_codes }>
                    <td>{ pcode.id }</td>
                    <td>{ pcode.sponsor }</td>
                    <td>{ pcode.codes }</td>
                    <td>
                        <a href="{ parent.edit_link+'/'+pcode.id }" class="btn btn-default btn-sm" role="button">Edit</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="panel panel-default" if={ page_data.total_items == 0 }>
        <span class="no_codes_msg"> No Promo Codes found.</span>
    </div>

    <nav>
    <ul id="promocode-pager" class="pagination"></ul>
    </nav>

        <script>

        this.promo_codes     = opts.promo_codes;
        this.page_data       = opts.page_data;
        this.summit_id       = opts.summit_id;
        this.edit_link       = opts.edit_link;
        this.total_pages     = 0;
        this.sort_by         = 'name';
        this.sort_dir        = 'asc';
        var self             = this;

        this.on('mount', function() {
            self.total_pages = self.page_data.total_items > 0 ? Math.ceil(self.page_data.total_items / self.page_data.limit): 0;
            var options = {
                bootstrapMajorVersion:3,
                currentPage: self.page_data.page ,
                totalPages: self.total_pages ,
                numberOfPages: 10,
                onPageChanged: function(e,oldPage,newPage){
                    self.getCodes(newPage);
                }
            };

            $('#promocode-pager').bootstrapPaginator(options);

            $('#search_promocode').click(function(e) {
                self.getCodes(1);
            });

            $("#promocode_search_term").keydown(function (e) {
                if (e.keyCode == 13) {
                    $('#search_promocode').click();
                }
            });
        });

        getCodes(page) {
            $('body').ajax_loader();
            var search_term = $('#promocode_search_term').val().trim();
            var type = $('#code_type').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/registration-codes/sponsors/all',{
                    page:page,
                    items:self.page_data.limit,
                    term: search_term,
                    type: type,
                    sort_by: self.sort_by,
                    sort_dir: self.sort_dir,
                },function(data){

                self.promo_codes           = data.codes;
                self.page_data.page        = data.page;
                self.page_data.total_items = data.count;
                self.total_pages           = self.page_data.total_items > 0 ? Math.ceil(self.page_data.total_items / self.page_data.limit): 0;

                if(self.promo_codes .length > 0){
                    var options = {
                        bootstrapMajorVersion:3,
                        currentPage: self.page_data.page ,
                        totalPages: self.total_pages,
                        numberOfPages: 10,
                        onPageChanged: function(e,oldPage,newPage){
                            self.getCodes(newPage);
                        }
                    };

                    $('#promocode-pager').bootstrapPaginator(options);
                    $('#promocode-pager').show();
                }
                else $('#promocode-pager').hide();
                self.update();
                $('body').ajax_loader('stop');
            });
        }

        sortBy(e) {
            var target = $(e.currentTarget);
            self.sort_by= target.attr('data-field');
            self.sort_dir= target.attr('data-dir');
            target.attr('data-dir', self.sort_dir === 'asc' ? 'desc' : 'asc');
            self.getCodes(1);
        }

        clearClicked(e){
            $('#promocode_search_term').val('');
            self.getCodes(1);
        }

        </script>
</promocode-sponsor-list>