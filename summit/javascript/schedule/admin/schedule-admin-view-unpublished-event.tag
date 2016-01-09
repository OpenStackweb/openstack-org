<schedule-admin-view-unpublished-event>

    <div class="event resizable event-unpublished" id="event_{ data.id }" data-id="{ data.id }">
        <div class="ui-resizable-handle ui-resizable-n" style="display:none">
            <span class="ui-icon ui-icon-triangle-1-n"></span>
        </div>
        <div class="event-inner-body">
            <div class="event-title">
                <a id="popover_{ data.id }" data-content="{ data.description }" title="{ data.title }" role="button" data-toggle="popover" data-trigger="focus">{ data.title }</a>
            </div>
        </div>
        <div class="ui-resizable-handle ui-resizable-s" style="display:none">
            <span class="ui-icon ui-icon-triangle-1-s"></span>
        </div>
    </div>

    <script>

        this.data          = opts.data;
        this.minute_pixels = parseInt(opts.minute_pixels);
        this.interval      = parseInt(opts.interval);
        var self           = this;

        this.on('mount', function() {
            $("#popover_"+self.data.id).popover({ trigger: "hover" });
        });

    </script>
</schedule-admin-view-unpublished-event>