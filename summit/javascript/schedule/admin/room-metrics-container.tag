<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<room-metrics-container>
    <div class="row">
        <div class="col-md-4">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="search-term" class="form-control input-global-search" placeholder="Search Event">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" onclick={ addEventChart }><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearSearch }>
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <br>

    <script>
        this.dispatcher = opts.dispatcher;
        this.summit_id  = opts.summit_id;
        var self        = this;

        this.on('mount', function() {
            $("#search-term").keydown(function (e) {
                if (e.keyCode == 13) {
                    self.addEventChart();
                } 
            });

        });

        addEventChart() {
            var event = $('#search-term').val();
            self.dispatcher.addEventChart(event);
        }

        clearSearch() {
            $('#search-term').val('');
        }

    </script>

</room-metrics-container>