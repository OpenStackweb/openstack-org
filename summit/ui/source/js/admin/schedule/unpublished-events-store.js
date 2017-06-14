
var api  = require('./schedule-admin-view-api.js');

function unpublishedEventsStore(){

    riot.observable(this);

    this.LOAD_STORE = 'UNPUBLISHED_EVENTS_STORE_LOADED';

    this._unpublished_events = {};
    this._page               = 1;
    this._page_size          = 10;
    this._total_pages        = null

    this.clear = function() {
        this._unpublished_events = {};
    }

    this.all = function(){
        return this._unpublished_events;
    }

    this.get = function(event_id) {
        return this._unpublished_events['ev_'+event_id];
    }

    this._setApiResponse = function (response)
    {
        this._load(response.data);
        this._page        = response.page;
        this._page_size   = response.page_size;
        this._total_pages = response.total_pages;
    }

    this.getPagesInfo = function()
    {
        return { page: this._page, total_pages: this._total_pages };
    }

    this.delete = function(event_id)
    {
        var item = this.get(event_id);
        delete this._unpublished_events['ev_'+event_id];
        self.trigger(self.LOAD_STORE);
        return item;
    }

    this._load = function(events) {
        this.clear();
        // update model
        for(var e of events) {
            // we need to make the array key a string to keep the order of the items
            this._unpublished_events['ev_'+e.id] = e;
        }
    }

    this.isEmpty = function() {
        return Object.keys(this._unpublished_events).length  == 0;
    }

    var self = this;

    api.on(api.RETRIEVED_UNPUBLISHED_EVENTS,function(response) {
        console.log(api.RETRIEVED_UNPUBLISHED_EVENTS);
        self._setApiResponse(response);
        self.trigger(self.LOAD_STORE);
    });
}


var store = new unpublishedEventsStore();

module.exports = store;
