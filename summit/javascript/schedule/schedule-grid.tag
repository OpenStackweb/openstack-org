<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<schedule-grid>
    <schedule-grid-nav month={ month } ></schedule-grid-nav>
    <div class="row">
            <div class="container" id="events-container">
               <schedule-main-filters summit="{ summit }" schedule_filters={ schedule_filters }></schedule-main-filters>
               <schedule-grid-events summit="{ summit }" default_event_color={ default_event_color } search_url={ search_url } schedule_filters={ schedule_filters } base_url={ base_url } schedule_api={ schedule_api }></schedule-grid-events>
            </div>
    </div>

    <script>

        this.summit                   = opts.summit;
        this.month                    = opts.month;
        this.schedule_api             = opts.schedule_api;
        this.schedule_filters         = opts.schedule_filters;
        this.search_url               = opts.search_url;
        this.base_url                 = opts.base_url;
        this.default_event_color      = opts.default_event_color;
        var self                      = this;

    </script>

</schedule-grid>