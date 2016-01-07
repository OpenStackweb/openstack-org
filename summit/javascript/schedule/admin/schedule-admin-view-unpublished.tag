<schedule-admin-view-unpublished>

    <ul class="list-unstyled unpublished-events-list">
        <li each="{ key, e in store.all() }">
            <schedule-admin-view-unpublished-event data="{ e }" minute_pixels="{ parent.minute_pixels }" interval="{ parent.interval }"></schedule-admin-view-unpublished-event>
        </li>
    </ul>

    <nav>
        <ul if="{ pages.length > 0 }" class="pagination">
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

        onPageChange(e) {
            console.log('page ' + e.item.number);
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
    </script>
</schedule-admin-view-unpublished>