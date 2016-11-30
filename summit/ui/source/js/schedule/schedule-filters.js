var schedule_filters = riot.observable();

schedule_filters.publishFiltersChanged = function(filters)
{
    console.log('scheduleFiltersChanged');
    schedule_filters.trigger('scheduleFiltersChanged', filters);
}

schedule_filters.toggleFilters = function(view)
{
    console.log('toggleFilters');
    var hide = false;
    switch (view) {
        case 'days':
            hide = false;
            break;
        case 'levels':
            hide = true;
            break;
        case 'tracks':
            hide = true;
            break;
    }
    schedule_filters.trigger('scheduleToggleFilters', hide);
}

module.exports = schedule_filters;