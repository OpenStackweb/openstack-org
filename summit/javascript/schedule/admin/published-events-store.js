var api  = require('./schedule-admin-view-api.js');


function publishedEventsStore(){
    riot.observable(this);

    this.LOAD_STORE = 'PUBLISHED_EVENTS_STORE_LOADED';

    this._published_events = {};
    this._summit_id        = null;
    this._location_id      = null;
    this._day              = null;

    this.currentLocation = function(){
        return this._location_id;
    }

    this.currentDay = function()
    {
        return this._day;
    }

    this.clear = function() {
        this._published_events = {};
    }

    this.all = function(){
        return this._published_events;
    }

    this.add = function(event)
    {
        this._published_events[event.id] = event;
    }

    this.get = function(event_id) {
        return this._published_events[event_id];
    }

    this.delete = function(event_id)
    {
        var item = this.get(event_id);
        delete this._published_events[event_id];
        self.trigger(self.LOAD_STORE);
        return item;
    }

    this._load = function(events) {
        this.clear();
        // update model

        for(var e of events) {
            this._published_events[e.id] = e;
            e.start_time = moment(this._day+' '+e.start_time, 'YYYY-MM-DD HH:mm');
            e.end_time   = moment(this._day+' '+e.end_time, 'YYYY-MM-DD HH:mm');
        }
    }
    var self = this;

    api.on(api.RETRIEVED_PUBLISHED_EVENTS,function(response) {
        console.log(api.RETRIEVED_PUBLISHED_EVENTS);

        self._summit_id   = response.summit_id;
        self._location_id = response.location_id;
        self._day         = response.day;
        self._load(response.events);
        self.trigger(self.LOAD_STORE);
    });
}


var store = new publishedEventsStore();

module.exports = store;