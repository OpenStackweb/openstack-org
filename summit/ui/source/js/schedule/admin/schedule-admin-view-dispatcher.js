function ScheduleAdminViewDispatcher() {

    riot.observable(this);

    this.PUBLISHED_EVENT                   = 'PUBLISHED_EVENT';
    this.PUBLISHED_EVENTS_FILTER_CHANGE    = 'PUBLISHED_EVENTS_FILTER_CHANGE';
    this.PUBLISHED_EVENTS_SEARCH           = 'PUBLISHED_EVENTS_SEARCH';
    this.PUBLISHED_EVENTS_SEARCH_EMPTY     = 'PUBLISHED_EVENTS_SEARCH_EMPTY';
    this.PUBLISHED_EVENTS_DEEP_LINK        = 'PUBLISHED_EVENTS_DEEP_LINK';
    this.UNPUBLISHED_EVENT                 = 'UNPUBLISHED_EVENT';
    this.UNPUBLISHED_EVENTS_PAGE_CHANGED   = 'UNPUBLISHED_EVENTS_PAGE_CHANGED';
    this.UNPUBLISHED_EVENTS_SOURCE_CHANGED = 'UNPUBLISHED_EVENTS_SOURCE_CHANGED';
    this.PUBLISHED_EVENTS_DAY_CHANGE       = 'PUBLISHED_EVENTS_DAY_CHANGE';

    this.publishEvent = function(event_id)
    {
        this.trigger(this.PUBLISHED_EVENT, event_id);
    }

    this.unPublishEvent = function(summit_id, event_id)
    {
        this.trigger(this.UNPUBLISHED_EVENT, summit_id, event_id);
    }

    this.unpublishedEventsPageChanged = function (page_nbr)
    {
        this.trigger(this.UNPUBLISHED_EVENTS_PAGE_CHANGED, page_nbr);
    }

    this.unpublishedEventsSourceChanged = function(source){
        this.trigger(this.UNPUBLISHED_EVENTS_SOURCE_CHANGED, source);
    }

    this.publishedEventsFilterChanged = function(summit_id, day ,location_id)
    {
        this.trigger(this.PUBLISHED_EVENTS_FILTER_CHANGE,summit_id ,day , location_id);
    }

    this.publishedEventsSearch = function(summit_id, term)
    {
        this.trigger(this.PUBLISHED_EVENTS_SEARCH,summit_id ,term);
    }

    this.publishedEventsSearchEmpty = function(summit_id, days, start_time, end_time, locations, length)
    {
        this.trigger(this.PUBLISHED_EVENTS_SEARCH_EMPTY,summit_id, days, start_time, end_time, locations, length);
    }

    this.publishedEventsDeepLink = function ()
    {
        this.trigger(this.PUBLISHED_EVENTS_DEEP_LINK);
    }

    this.publishedEventsDayChanged = function(summit_id, day)
    {
        this.trigger(this.PUBLISHED_EVENTS_DAY_CHANGE, summit_id ,day);
    }
}

var dispatcher = new ScheduleAdminViewDispatcher();

module.exports = dispatcher;