function ScheduleAdminViewDispatcher() {

    riot.observable(this);

    this.PUBLISHED_EVENT   = 'PUBLISHED_EVENT';
    this.UNPUBLISHED_EVENT = 'UNPUBLISHED_EVENT';
    this.UNPUBLISHED_EVENTS_PAGE_CHANGED = 'UNPUBLISHED_EVENTS_PAGE_CHANGED';
    this.UNPUBLISHED_EVENTS_SOURCE_CHANGED = 'UNPUBLISHED_EVENTS_SOURCE_CHANGED';

    this.publishEvent = function(event_id)
    {
        this.trigger(this.PUBLISHED_EVENT, event_id);
    }

    this.unPublishEvent = function(event_id)
    {
        this.trigger(this.UNPUBLISHED_EVENT, event_id);
    }

    this.unpublishedEventsPageChanged = function (page_nbr)
    {
        this.trigger(this.UNPUBLISHED_EVENTS_PAGE_CHANGED, page_nbr);
    }

    this.unpublishedEventsSourceChanged= function(source){
        this.trigger(this.UNPUBLISHED_EVENTS_SOURCE_CHANGED, source);
    }
}

var dispatcher = new ScheduleAdminViewDispatcher();

module.exports = dispatcher;