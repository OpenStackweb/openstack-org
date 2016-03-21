<schedule-grid-nav>
    <div class="row navbar-container">
        <div class="col-md-3 view-select-container">
            View by
            <select id="view-select">
                <option value="days" selected>Day</option>
                <option value="tracks">Track</option>
                <option value="levels">Level</option>
            </select>
        </div>
        <div class="col-md-9">
            <nav class="navbar navbar-default navbar-days" if={ view == 'days' }>
                <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand navbar-brand-month" href="{ base_url }" onclick={ selectDate } >{ month }</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="{ active: day.selected, day-selected: day.selected }" each={  key, day in summit.dates } >
                        <a href="#" class="day-label" onclick={ selectDate } >{ day.label }</a>
                    </li>
                </ul>
                </div>
                </div>
            </nav>
            <div class="track-nav-container" if={ view == 'tracks' }>
                <select id="track-select" onchange={ setSelectedTrack }>
                    <option each={  key, cat in summit.category_groups } value="{ key }">{ cat.name }</option>
                </select>
            </div>
            <nav class="navbar navbar-default navbar-days" if={ view == 'levels' }>
                <div class="container">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li class="{ active: level.selected, level-selected: level.selected }" each={  key, level in summit.presentation_levels } >
                                <a href="#" class="level-label" onclick={ selectLevel } >{ level.level }</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <script>
        this.month             = opts.month;
        this.summit            = this.parent.summit;
        this.base_url          = this.parent.base_url;
        this.schedule_api      = this.parent.schedule_api;
        this.view              = 'days';
        var self               = this;

        this.on('mount', function(){
            var filter_day = $(window).url_fragment('getParam','day');
            if(filter_day === null){
                var now    = new Date();
                var year   = now.getUTCFullYear();
                var month  = self.pad(now.getUTCMonth()+1,2);
                var day    = self.pad(now.getUTCDate(),2);
                filter_day = year+'-'+month+'-'+day;
                console.log('current date key '+filter_day);
                if(typeof(self.summit.dates[filter_day]) === 'undefined'){
                    filter_day = Object.keys(self.summit.dates)[0];
                    for (adate in self.summit.dates) {
                        if (self.summit.dates[adate].has_events) {
                            filter_day = adate;
                            break;
                        }
                    }
                }
            }

            $('#view-select').change(function(){
                self.view = $(this).val();
                self.update();
            });

            self.setSelectedDay(self.summit.dates[filter_day]);
        });

        selectDate(e) {
            self.setSelectedDay(e.item.day);
        }

        setSelectedDay(day) {
            day.selected = true;
            $(window).url_fragment('setParam','day', day.date);
            window.location.hash = $(window).url_fragment('serialize');

            for(var d in self.summit.dates){
                d = self.summit.dates[d];
                if(d.date !== day.date){
                    d.selected = false;
                }
            }
            self.update();
            self.schedule_api.getEventByDay(self.summit.id, day.date);
        }

        selectLevel(e) {
            self.setSelectedLevel(e.item.level);
        }

        setSelectedLevel(level) {
            level.selected = true;
            $(window).url_fragment('setParam','level', level.level);
            window.location.hash = $(window).url_fragment('serialize');

            for(var l in self.summit.presentation_levels){
                l = self.summit.presentation_levels[l];
                if(l.level !== level.level){
                    l.selected = false;
                }
            }
            self.update();
            self.schedule_api.getEventByLevel(self.summit.id, level.level);
        }

        setSelectedTrack() {
            var track = $('#track-select').val();
            $(window).url_fragment('setParam','track', track);
            window.location.hash = $(window).url_fragment('serialize');
            self.summit.presentation_levels[track].selected = true;
            for(var c in self.summit.category_groups){
                if(c !== track){
                    self.summit.presentation_levels[c].selected = false;
                }
            }
            self.update();
            self.schedule_api.getEventByTrack(self.summit.id, track);
        }

        pad(num, size) {
            var s = num+"";
            while (s.length < size) s = "0" + s;
            return s;
        }
    </script>
</schedule-grid-nav>