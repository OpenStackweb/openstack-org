<schedule-global-filter>
    <div class="row global-search-container">
        <form id="form-schedule-global-search" name="form-schedule-global-search' class="form-inline all-events-search-form" method="get" action="{ opts.search_url }">
            <div class="col-xs-12 col-sm-4 col-sm-offset-8">
                <div class="input-group" style="width: 100%;">
                    <input data-rule-required="true" data-rule-minlength="3" value="{opts.value}" type="text" id="t" name="t" class="form-control input-global-search" placeholder="Search for Events/People">
                    <span class="input-group-btn" style="width: 5%;">
                        <button class="btn btn-default btn-global-search" type="submit"><i class="fa fa-search"></i></button>
                        <button if={ opts.clear_url } class="btn btn-default btn-global-search-clear" onclick={ clearClicked }><i class="fa fa-times"></i></button>
                     </span>
                </div>
            </div>
        </form>
    </div>
    <script>

    var self = this;

    this.on('mount', function(){
        $('#form-schedule-global-search').validate({
            errorPlacement: function(error, element) {
                error.insertAfter($('.input-group','#form-schedule-global-search'));
            }
        });
    });

    clearClicked(e){
        window.location = opts.clear_url;
    }

    </script>
</schedule-global-filter>