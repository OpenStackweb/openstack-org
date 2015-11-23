<schedule-grid-nav>
    <div class="row">
        <nav class="navbar navbar-default navbar-days">
            <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand navbar-brand-month" href="{ base_url }">{ month }</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
            <li class="{ active: selected_day.date == date, day-selected: selected_day.date == date }" each={ summit.dates } ><a href="#" class="day-label" onclick={ selectDate }>{ label }</a></li>
            </ul>
            </div>
            </div>
        </nav>
    </div>

    <script>
        this.month             = opts.month;
        this.summit            = this.parent.summit;
        this.selected_day      = this.summit.dates[0];
        this.schedule_api      = this.parent.schedule_api;
        this.base_url          = this.parent.base_url;
        this.aux_selected_day  = null;
        var self               = this;

        selectDate(e) {
            var day = e.item;
            self.schedule_api.getEventByDay(self.summit.id, day.date);
            self.aux_selected_day = day;
        }

        this.schedule_api.on('eventsRetrieved',function(data) {
            self.selected_day            = self.aux_selected_day;
            self.aux_selected_day        = null;
            self.update();
        });
    </script>
</schedule-grid-nav>