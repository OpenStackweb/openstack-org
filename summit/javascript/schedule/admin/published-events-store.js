var api        = require('./schedule-admin-view-api.js');
var dispatcher = require('./schedule-admin-view-dispatcher.js');

function publishedEventsStore(){
    riot.observable(this);

    this.LOAD_STORE       = 'PUBLISHED_EVENTS_STORE_LOADED';
    this.LOAD_RESULTS     = 'PUBLISHED_EVENTS_RESULTS_LOADED';
    this.LOAD_EMPTY_SPOTS = 'PUBLISHED_EMPTY_SPOTS_LOADED';

    this._published_events  = {};
    this._published_results = {};
    this._empty_spots       = {};
    this._summit_id         = null;
    this._location_id       = null;
    this._day               = null;

    this.currentLocation = function(){
        return this._location_id;
    }

    this.currentDay = function()
    {
        return this._day;
    }

    this.clear = function() {
        this._published_events = {};
        this._published_results = {};
        this._empty_spots = {};
    }

    this.all = function(){
        return this._published_events;
    }

    this.results = function(){
        return this._published_results;
    }

    this.empty_spots = function(){
        return this._empty_spots;
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
            e.start_datetime = moment(e.start_datetime, 'YYYY-MM-DD HH:mm:ss');
            e.end_datetime   = moment(e.end_datetime, 'YYYY-MM-DD HH:mm:ss');
        }
    }

    this._load_results = function(events) {
        this.clear();
        // update model

        for(var e of events) {
            this._published_results[e.id] = e;
        }
    }

    this._load_empty_spots = function(spots) {
        this.clear();
        // update model

        for(var s of spots) {
            if(!(s.location_id in this._empty_spots)) this._empty_spots[s.location_id] = [];
            this._empty_spots[s.location_id].push(s);
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

    api.on(api.RETRIEVED_PUBLISHED_SEARCH,function(response) {
        console.log(api.RETRIEVED_PUBLISHED_SEARCH);

        self._summit_id   = response.summit_id;
        self._load_results(response.events);
        self.trigger(self.LOAD_RESULTS);
    });

    api.on(api.RETRIEVED_EMPTY_SPOTS,function(response) {
        console.log(api.RETRIEVED_EMPTY_SPOTS);

        self._summit_id   = response.summit_id;
        self._load_empty_spots(response.empty_spots);
        self.trigger(self.LOAD_EMPTY_SPOTS);
    });
}


var store = new publishedEventsStore();

dispatcher.on(dispatcher.UNPUBLISHED_EVENT, function(summit_id, event_id){
    store.delete(event_id);
});

module.exports = store;