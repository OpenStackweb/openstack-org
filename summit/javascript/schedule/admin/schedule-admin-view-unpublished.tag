<schedule-admin-view-unpublished>

    <ul class="list-unstyled unpublished-events-list">
        <li each="{ key, e in store.all() }">
            <schedule-admin-view-unpublished-event data="{ e }" minute_pixels="{ parent.minute_pixels }" interval="{ parent.interval }"></schedule-admin-view-unpublished-event>
        </li>
    </ul>

    <nav class="unpublished-events-pager">
        <ul if="{ pages.length > 1 }" class="pagination">
                <li class="disabled">
                    <span aria-hidden="true">&laquo;</span>
                </li>
                <li each={ pages } class="{ active: current }"><a href="#" onclick="{ onPageChange }" >{ number }</a></li>
                <li>
                    <span aria-hidden="true">&raquo;</span>
                </li>
        </ul>
    </nav>
    <script>

        this.summit             = opts.summit;
        this.minute_pixels      = opts.minute_pixels;
        this.interval           = opts.interval;
        this.api                = opts.api;
        this.dispatcher         = opts.dispatcher;
        this.store              = opts.unpublished_store;
        this.pages              = [];
        var self                = this;



        this.on('mount', function() {

        });

        onPageChange(e) {
            console.log('page ' + e.item.number);
            self.dispatcher.unpublishedEventsPageChanged(e.item.number);
        }

        self.store.on(self.store.LOAD_STORE,function() {
            console.log('UI: '+self.store.LOAD_STORE);
            // update UI
            $(".event-unpublished").remove();
            var page_info = self.store.getPagesInfo();
            self.pages    = [];
            for(var i=0 ; i < page_info.total_pages ; i++){
                self.pages.push({ number: (i+1), current: page_info.page == (i+1)});
            }
            self.update();
            $('[data-toggle="popover"]').popover({
                trigger: 'hover focus',
                html: true,
                container: 'body',
                placement: 'auto',
                animation: true,
                template : '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });
            self.createDraggable($(".event-unpublished"));
        });

        createDraggable(selector) {
            selector.draggable({
                containment: "document",
                cursor: "move",
                helper: "clone",
                opacity: 0.35
            });
        }

        self.dispatcher.on(self.dispatcher.UNPUBLISHED_EVENTS_SOURCE_CHANGED, function(source){
            if(source === '')
            {
                $('.unpublished-events-list').hide();
                $('.unpublished-events-pager').hide();
            }
            else
            {
                $('.unpublished-events-list').show();
                $('.unpublished-events-pager').show();
            }
        });
    </script>
</schedule-admin-view-unpublished>