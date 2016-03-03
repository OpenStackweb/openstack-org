<schedule-admin-view-empty-spots>
    <div id="empty_spots" style="display:none">
        <h4>Empty Spots</h4>
        <div each="{ location, spot in published_store.empty_spots() }" >
            <hr>
            <h5>{ summit.locations_dictionary[location].name }</h5>
            <div class="list-group">
                <a href="#" onclick={ parent.deepLinkToSpot } class="list-group-item" each={ spot } >
                    <h4 class="list-group-item-heading">
                        { summit.dates[day].label } - From: { moment(time, 'HH:mm').format("hh:mm A") } - Gap: { gap }
                    </h4>
                </a>
            </div>
        </div>
    </div>
    <script>

        this.published_store   = opts.published_store;
        this.summit            = summit;
        this.api               = opts.api;
        this.dispatcher        = opts.dispatcher;
        var self               = this;

        self.published_store.on(self.published_store.LOAD_EMPTY_SPOTS,function() {
            console.log('UI: '+self.published_store.LOAD_EMPTY_SPOTS);
            self.update();
            $('body').ajax_loader('stop');
        });

        deepLinkToSpot(event) {
            var item = event.item;

            window.location.hash='';
            $(window).url_fragment('setParam','day', item.day);
            $(window).url_fragment('setParam','venue', item.location_id);
            $(window).url_fragment('setParam','time', item.time);
            window.location.hash =  $(window).url_fragment('serialize');

            self.dispatcher.publishedEventsDeepLink();
        }


    </script>
</schedule-admin-view-empty-spots>