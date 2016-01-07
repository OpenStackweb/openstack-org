function ScheduleAdminViewDispatcher() {

    riot.observable(this);

    this.PUBLISHED_EVENT   = 'PUBLISHED_EVENT';
    this.UNPUBLISHED_EVENT = 'UNPUBLISHED_EVENT';

    this.publishEvent = function(event_id)
    {
        this.trigger(this.PUBLISHED_EVENT, event_id);
    }

    this.unPublishEvent = function(event_id)
    {
        this.trigger(this.UNPUBLISHED_EVENT, event_id);
    }

}

var dispatcher = new ScheduleAdminViewDispatcher();

module.exports = dispatcher;