<schedule-admin-view-unpublished-event>

    <div class="event resizable event-unpublished unselectable { getCSSClassBySelectionStatus(data.status) }" id="event_{ data.id }" data-id="{ data.id }">
        <div class="ui-resizable-handle ui-resizable-n" style="display:none">
            <span class="ui-icon ui-icon-triangle-1-n"></span>
        </div>
        <div class="event-buttons">
            <a style="display:none" class="unpublish-event-btn" title="unpublish event" data-event-id="{ data.id }"><i class="fa fa-times"></i></a>
            <a href="summit-admin/{ parent.summit.id }/events/{ data.id }" class="edit-event-btn" title="edit event">
                <i class="fa fa-pencil-square-o"></i>
            </a>
        </div>
        <div class="event-inner-body">
            <div>
                <a id="popover_{ data.id }" data-content="{ getPopoverContent() }" title="{ data.title }" data-toggle="popover">{ data.title.substring(0, 70) }{ data.title.length > 70 ? '...':''}{ data.class_name === 'Presentation'?' - '+parent.summit.tracks_dictionary[data.track_id].name:'' }</a>
            </div>
            <div class="presentation-status">
                <div if={ data.status }  class="event-status-component" title="status"><i class="fa fa-check-circle">&nbsp;{data.status}</i></div>
            </div>
        </div>
        <div class="ui-resizable-handle ui-resizable-s" style="display:none">
            <span class="ui-icon ui-icon-triangle-1-s"></span>
        </div>
    </div>

    <script>

        this.data          = opts.data;
        this.summit        = parent.summit;
        this.minute_pixels = parseInt(opts.minute_pixels);
        this.interval      = parseInt(opts.interval);
        var self           = this;


        this.on('mount', function() {

        });

        getPopoverContent() {
            var description = self.data.abstract != null ? self.data.abstract : self.data.description;
            if(description == null) description = 'TBD';
            var res = '<div class="row"><div class="col-md-12">'+description+'</div></div>';
            if(typeof(self.data.speakers) !== 'undefined') {
                res += '<div class="row"><div class="col-md-12"><b>Speakers</b></div></div>';
                for(var idx in self.data.speakers) {
                    var speaker = self.data.speakers[idx];
                    res += '<div class="row"><div class="col-md-12">'+ speaker.name+'</div></div>';
                }
            }
            return res;
        }

        getCSSClassBySelectionStatus(status) {
            switch(status){
                case 'accepted':return 'status-accepted';break;
                case 'alternate':return 'status-alternate';break;
                case 'unaccepted':return 'status-unaccepted';break;
                default: return '';break;
            }
            return '';
        }

    </script>
</schedule-admin-view-unpublished-event>