 <event-action-buttons>

  <div class="row info_item event-actions">

     <button if={ this.current_user.is_attendee && this.event.has_rsvp && this.event.rsvp_external}
                  id="btn_rsvp_external"
                  title="{ this.event.going ? 'UnSchedule': 'RSVP' }"
                  type="button"
                  onclick={ toggleExternalRSVP }
                  class="btn btn-primary btn-md active btn-rsvp-own-event btn-action { this.event.going ? 'btn-action-pressed': 'btn-action-normal' }">
             <span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<span class="content">{ this.event.going ? 'Schedule': 'RSVP' }</span>
     </button>
     <button if={ this.current_user.is_attendee && this.event.has_rsvp && !this.event.rsvp_external}
             id="btn_rsvp_own"
             title="{ this.event.going ? 'unRSVP': 'RSVP' }"
             type="button"
             onclick={ toogleRSVPState }
             class="btn btn-primary btn-md active btn-rsvp-own-event btn-action { this.event.going ? 'btn-action-pressed': 'btn-action-normal' } { !this.event.going && this.event.rsvp_seat_type == 'FULL' ? 'btn-full-rsvp': '' }">
        <span class="glyphicon { getRSVPIcon() }"></span>&nbsp;<span class="content">{ getOwnRSVPText() }</span>
     </button>
     <button if={ this.current_user.is_attendee && !this.event.has_rsvp }
             id="btn_schedule"
             title="{ this.event.going ? 'UnSchedule': 'Schedule' }"
             type="button"
             onclick={ toogleScheduleState }
             class="btn btn-primary btn-md active btn-schedule-event btn-action { this.event.going ? 'btn-action-pressed': 'btn-action-normal' }">
        <span class="glyphicon { getScheduleIcon() }"></span>&nbsp;<span class="content">Schedule</span>
     </button>

     <button id="btn_favorite"
                  title="{ this.event.favorite ? 'Do not Watch Later': 'Watch Later' }"
                  type="button"
                  onclick={ toogleFavoriteState }
                  class="btn btn-primary btn-md active btn-favorite-event btn-action { this.event.favorite ? 'btn-action-pressed': 'btn-action-normal' }">
             <i class="fa { getFavoriteIcon() }" aria-hidden="true"></i></span>&nbsp;<span class="content">Watch Later</span>
     </button>

     <div id="rsvpModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">RSVP</h4>
        </div>
        <div id="rsvpModalBody" class="modal-body">
            <span>Loading ...</span>
        </div>
        </div>
        </div>
     </div>
  </div>

  <script>

     this.event        = opts.event;
     this.current_user = opts.current_user;
     this.schedule_api = opts.schedule_api;
     var self          = this;

     this.on('mount', function(){
        $('#rsvpModal').modal({
             keyboard : false,
             show     : false
        })
     });

     this.schedule_api.on('addedEvent2MySchedule', function(event_id){
         if(self.event.has_rsvp && self.event.rsvp_external){
             var url      = new URI(event.rsvp_link);
             url.addQuery('BackURL', window.location)
             window.location = url.toString();
         }
     });

     getOwnRSVPText(){
        if(!self.event.going && self.event.rsvp_seat_type == 'FULL') return 'RSVP FULL';
        if(!self.event.going && self.event.rsvp_seat_type == 'Regular') return 'RSVP';
        if(!self.event.going && self.event.rsvp_seat_type == 'WaitList') return 'RSVP (WaitList)';
        return 'RSVP';
     }

     getRSVPIcon(){
        if(!self.event.going && self.event.rsvp_seat_type == 'FULL' ) return 'glyphicon-warning-sign';
        if(self.event.going) return 'glyphicon-ok-sign';
        // default (not going)
        return 'glyphicon-ok-circle';
     }

     getFavoriteIcon(){
        if(self.event.favorite) return 'fa-bookmark';
        // default (not favorite)
        return 'fa-bookmark-o';
     }

     getScheduleIcon(){
       if(self.event.going) return 'glyphicon-ok-sign';
       // default (not going)
       return 'glyphicon-ok-circle';
     }

     toogleScheduleState(e){
        var former_state = self.event.going;
        self.event.going = !former_state;
        if(former_state){
           self.schedule_api.removeEventFromMySchedule(self.event.summit_id, self.event.id);
        }
        else{
           self.schedule_api.addEvent2MySchedule(self.event.summit_id, self.event.id);
        }
        e.preventDefault();
        e.stopPropagation();
        self.update();
        return false;
     }

     toogleFavoriteState(e){
        var former_state = self.event.favorite;
        self.event.favorite = !former_state;
        if(former_state){
              self.schedule_api.removeEventFromMyFavorites(self.event.summit_id, self.event.id);
        }
        else{
             self.schedule_api.addEvent2MyFavorites(self.event.summit_id, self.event.id);
        }
        self.update();
        e.preventDefault();
        e.stopPropagation();
        return false;
     }

     toogleRSVPState(e){
        var former_state = self.event.going;
        e.preventDefault();
        e.stopPropagation();
        if(!former_state && self.event.rsvp_seat_type == 'FULL'){
            return false;
        }

        self.event.going = !former_state;
        self.update();

        if(former_state){
            //unRSVP
             self.schedule_api.unRSVPEvent(self.event.summit_id, self.event.id);
        }
        else{
            // open modal
            var modal       = $('#rsvpModal');
            var uri         = new URI( window.location);
            $('#rsvpModalBody').load(uri.segment('rsvp').toString(),function(result){

            });
            modal.modal('show');
        }
        return false;
     }

     toggleExternalRSVP(e){
        self.toogleScheduleState(e);
        var url      = new URI(self.event.rsvp_link);
        url.addQuery('BackURL', window.location)
        window.location = url.toString();
        e.preventDefault();
        e.stopPropagation();
        return false;
     }

  </script>

 </event-action-buttons>
