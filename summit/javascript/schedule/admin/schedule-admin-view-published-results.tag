<schedule-admin-view-published-results>
    <div id="search_results" style="display:none">
        <h4>Search Results</h4>
        <div class="list-group">
            <a href="#" onclick={ parent.deepLinkToEvent } class="list-group-item" each="{ key, e in published_store.results() }" >
                <h4 class="list-group-item-heading" id="popover_{ e.id }" data-content="{ getPopoverContent(e) }" title="{ e.title }" data-toggle="popover">{ e.title }</h4>
            </a>
        </div>
    </div>
    <script>

        this.published_store   = opts.published_store;
        this.summit            = summit;
        this.api               = opts.api;
        this.dispatcher        = opts.dispatcher;
        var self               = this;

        self.published_store.on(self.published_store.LOAD_RESULTS,function() {
            console.log('UI: '+self.published_store.LOAD_RESULTS);
            self.update();
            $('[data-toggle="popover"]').popover({
                trigger: 'hover focus',
                html: true,
                container: 'body',
                placement: 'auto',
                animation: true,
                template : '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });

            $('body').ajax_loader('stop');

        });

        getPopoverContent(event) {
            var res = '<div class="row"><div class="col-md-12">'+event.description+'</div></div>';
            if(typeof(event.speakers) !== 'undefined') {
                res += '<div class="row"><div class="col-md-12"><b>Speakers</b></div></div>';
                for(var idx in event.speakers) {
                    var speaker = event.speakers[idx];
                    res += '<div class="row"><div class="col-md-12">'+ speaker.name+'</div></div>';
                }
            }
            return res;
        }

        deepLinkToEvent(event) {
            var item = event.item;

            $(window).url_fragment('setParam','day', item.e.start_date);
            $(window).url_fragment('setParam','venue', item.e.location_id);
            $(window).url_fragment('setParam','event', item.e.id);
            window.location.hash =  $(window).url_fragment('serialize');

            $('[data-toggle="popover"]').each(function() {
                $(this).popover('hide');
            });

            self.dispatcher.publishedEventsDeepLink();
        }


    </script>
</schedule-admin-view-published-results>