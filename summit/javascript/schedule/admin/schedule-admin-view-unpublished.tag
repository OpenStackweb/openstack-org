<schedule-admin-view-unpublished>

    <ul class="list-unstyled unpublished-events-list">
        <li each="{ key, e in store.all() }">
            <schedule-admin-view-unpublished-event data="{ e }" minute_pixels="{ parent.minute_pixels }" interval="{ parent.interval }"></schedule-admin-view-unpublished-event>
        </li>
    </ul>
    <div>
        <ul id="unpublished-events-pager"></ul>
    </div>
    <script>

        this.summit             = opts.summit;
        this.minute_pixels      = opts.minute_pixels;
        this.interval           = opts.interval;
        this.api                = opts.api;
        this.dispatcher         = opts.dispatcher;
        this.store              = opts.unpublished_store;
        var self                = this;

        this.on('mount', function() {
            $( window ).resize(function() {
                 $('.event-unpublished').css('width', $('.time-slot-container').width());
            });
        });

        self.store.on(self.store.LOAD_STORE,function() {
            console.log('UI: '+self.store.LOAD_STORE);
            // update UI
            $(".event-unpublished").remove();
            var page_info = self.store.getPagesInfo();
            self.update();
            var options = {
                bootstrapMajorVersion:3,
                currentPage: page_info.page ,
                totalPages: page_info.total_pages,
                numberOfPages: 10,
                onPageChanged: function(e,oldPage,newPage){
                    $('#alert-content').text("Current page changed, old: "+oldPage+" new: "+newPage);
                    console.log('page ' + newPage);
                    self.dispatcher.unpublishedEventsPageChanged(newPage);
                }
            }
            $('#unpublished-events-pager').bootstrapPaginator(options);
            $('[data-toggle="popover"]').popover({
                trigger: 'hover focus',
                html: true,
                container: 'body',
                placement: 'auto',
                animation: true,
                template : '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });
            self.createDraggable($(".event-unpublished"));
            $('.event-unpublished').css('width', $('.time-slot-container').width());
            $('body').ajax_loader('stop');
        });

        createDraggable(selector) {
            selector.draggable({
                containment: "document",
                cursor: "move",
                helper: "clone",
                opacity: 0.5
            });
        }

        self.dispatcher.on(self.dispatcher.UNPUBLISHED_EVENTS_SOURCE_CHANGED, function(source){
            if(source === '')
            {
                $('.unpublished-events-list').hide();
                $('#unpublished-events-pager').hide();
            }
            else
            {
                $('.unpublished-events-list').show();
                $('#unpublished-events-pager').show();
            }
        });
    </script>
</schedule-admin-view-unpublished>