<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<events-bulk-container>
    <div class="row">
        <div class="col-md-4">
            <select id="list_select" class="form-control" >
                <option value="presentations"> Presentations </option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success" id="save-list" onclick={ saveList } >Save</button>
        </div>
        <div class="col-md-6">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="search-term" class="form-control input-global-search" placeholder="Search Speaker or Presentation">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" onclick={ searchList }><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearSearch }>
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <br>

    <events-bulk-presentation-list if={list == 'presentations'} page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></events-bulk-presentation-list>

    <script>
        this.list       = opts.list;
        this.dispatcher = opts.dispatcher;
        this.summit_id  = opts.summit_id;
        this.limit      = opts.limit;
        var self        = this;

        this.on('mount', function() {
            $("#list_select").change(function(){
                self.list = $(this).val();
                self.update();
            });

            $("#search-term").keydown(function (e) {
                if (e.keyCode == 13) {
                    self.searchList();
                }
            });

        });

        saveList(e) {
            var list = $('#list_select').val();
            if (typeof(e) !== 'undefined') {
                e.preventUpdate = true;
            }
            self.dispatcher.saveList(list);
        }

        searchList() {
            var list = $('#list_select').val();
            self.dispatcher.getList(list);
        }

        clearSearch() {
            $('#search-term').val('');
            var list = $('#list_select').val();
            self.dispatcher.getList(list);
        }

    </script>

</events-bulk-container>