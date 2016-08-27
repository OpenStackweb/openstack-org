<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<promocode-list>
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
        <div class="col-md-3" style="margin:0  0 20px 0;">
            <label>Type</label>
            <select id="code_type">
                <option value="">ALL</option>
                <option value="{ code_type }" each={ code_type, i in promocode_types }>{ code_type }</option>
            </select>
        </div>
        <div class="col-md-3" style="margin:0  0 20px 0;">
            <button onclick="{ exportCodes }" class="btn btn-primary">Export</button>
        </div>
    </div>

    <div class="panel panel-default" if={ page_data.total_items > 0 }>
        <div class="panel-heading">Promo Codes ({ page_data.total_items })</div>

        <table id="promocode-table" class="table">
            <thead>
                <tr>
                    <th><a title="sort by Code" style="cursor:pointer;" onclick="{ sortBy }" data-field='code' data-dir='asc'>Code</a></th>
                    <th><a title="sort by Type" style="cursor:pointer;" onclick="{ sortBy }" data-field='class' data-dir='asc'>Type</a></th>
                    <th>Owner</th>
                    <th>Owner Email</th>
                    <th>Emailed</th>
                    <th>Redeemed</th>
                    <th>Source</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ pcode, i in promo_codes }>
                    <td>{ pcode.code }</td>
                    <td>{ pcode.type }</td>
                    <td>{ pcode.owner }</td>
                    <td>{ pcode.owner_email }</td>
                    <td>
                        <i class="fa { pcode.email_sent ? 'fa-check' : 'fa-times'}" aria-hidden="true"></i>
                    </td>
                    <td>
                        <i class="fa { pcode.redeemed ? 'fa-check' : 'fa-times'}" aria-hidden="true"></i>
                    </td>
                    <td>{ pcode.source }</td>
                    <td>
                        <a href="{ parent.edit_link+'/'+pcode.code }" class="btn btn-default btn-sm" role="button" if="{ !pcode.email_sent }">Edit</a>
                        <a href="" onclick="{ deleteCode }" class="btn btn-danger btn-sm" role="button" if="{ !pcode.email_sent }">Delete</a>
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
        this.promocode_types = opts.promocode_types;
        this.page_data       = opts.page_data;
        this.summit_id       = opts.summit_id;
        this.edit_link       = opts.edit_link;
        this.total_pages     = 0;
        this.sort_by         = 'code';
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

            $('#code_type').change(function(e) {
                self.getCodes(1);
            });
        });

        getCodes(page) {
            $('body').ajax_loader();
            var search_term = $('#promocode_search_term').val().trim();
            var type = $('#code_type').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/registration-codes/all',{
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

        deleteCode(e) {
            swal(
                {
                    title: "Delete Promo Code",
                    text: "Are you sure you want to delete this promo code?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(){
                    e.preventDefault();
                    $.ajax({
                        url: 'api/v1/summits/'+self.summit_id+'/registration-codes/'+e.item.pcode.id,
                        type: 'DELETE',
                        success: function(result) {
                            $(e.target).parents('tr').remove();
                            swal('Deleted', 'Promo Code deleted successfully', 'success');
                        }
                    });
                });

            return false;
        }

        exportCodes() {
            var search_term = $('#promocode_search_term').val().trim();
            var type = $('#code_type').val();

            window.open('api/v1/summits/'+self.summit_id+'/registration-codes/export?term='+search_term+'&type='+type, '_blank');
        }

        </script>
</promocode-list>