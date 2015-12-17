var schedule_filters = riot.observable();

schedule_filters.publishFiltersChanged = function(filters)
{
    console.log('scheduleFiltersChanged');
    schedule_filters.trigger('scheduleFiltersChanged', filters);
}

module.exports = schedule_filters;