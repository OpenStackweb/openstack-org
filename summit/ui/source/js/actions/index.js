import ScheduleApi from '../schedule/schedule-api'

export const ScheduleProps = {
    month: null,
    summit: {},
    base_url: null,
    search_url: null,
}

// Reducers
export const CHANGE_VIEW = 'CHANGE_VIEW'
export const SET_FILTERS = 'SET_FILTERS'
export const UPDATE_EVENT = 'UPDATE_EVENT'
export const CALENDAR_SYNC = 'CALENDAR_SYNC'
export const TOGGLE_FILTERS = 'TOGGLE_FILTERS'
export const RECEIVE_EVENTS = 'RECEIVE_EVENTS'
export const BULK_SYNC_TOGGLE_ALL = 'BULK_SYNC_TOGGLE_ALL'
export const BULK_SYNC_TOGGLE_EVENT = 'BULK_SYNC_TOGGLE_EVENT'

// Application constants.
export const DEFAULT_FILTERS = {
    track_groups : null,
    event_types  : null,
    tracks       : null,
    tags         : null,
    levels       : null,
    going        : false,
    favorites    : false,
}

export const VIEW_DAYS   = 'day'
export const VIEW_TRACKS = 'track'
export const VIEW_LEVELS = 'level'

export const DEFAULT_VIEWS = {
    [VIEW_DAYS]: null,
    [VIEW_TRACKS]: null,
    [VIEW_LEVELS]: null,
}

const CALENDAR_ERROR_MSG = 'Some events failed to update.';

export const EVENT_FIELD_DETAIL = '_detail'
export const EVENT_FIELD_LOADED = '_loaded'
export const EVENT_FIELD_EXPANDED = '_expanded'

ScheduleApi.on('error', () => $('#events-container').ajax_loader('stop'))

// Redux Action creators.
export const loadFilters = () => {
    return dispatch => {
        let view = null
        let values = {}

        Object.keys(DEFAULT_FILTERS).forEach(filterName => {
            const value = getUrlParam(filterName)
            if (value) values[filterName] = value.split(',')
        });

        Object.keys(DEFAULT_VIEWS).forEach(type => {
            const value = getUrlParam(type)
            if (value) view = { type, value }
        });

        view = view || { type: VIEW_DAYS, value: getDefaultViewDay() }

        dispatch(changeView(view.type, view.value))
        dispatch(setFilters(values, Object.keys(values).length > 0))
    }
}

export const autoloadEvent = (events, view, filtered) => {
    return dispatch => {
        const autoloadId = getAutoloadEventId(events, view)

        if (filtered.indexOf(autoloadId) >= 0) {
            return // Do not autoload filtered events.
        }

        const event = autoloadId && events.filter(
            event => event.id === autoloadId
        ).shift()

        if (event) {
            $('html, body').animate({
                scrollTop: $(`#event_${event.id}`).offset().top
            }, 1000).promise().then(() => {
                dispatch(toggleEventDetail(event))
            })
        }
    }
}

export const setFilters = (values, expanded) => {
    return {
        type: SET_FILTERS,
        payload: { values, expanded }
    }
}

export const clearFilters = () => {
    return setFilters({ ...DEFAULT_FILTERS }, false)
}

export const toggleFilters = () => {
    return { type: TOGGLE_FILTERS }
}

export const changeView = (type, value) => {
    return dispatch => {

        const { summit } = ScheduleProps

        ScheduleApi.one('beforeEventsRetrieved', schedule => {
            $('#events-container').ajax_loader()
        })

        ScheduleApi.one('eventsRetrieved', schedule => {
            $('#events-container').ajax_loader('stop')
            dispatch({ type: RECEIVE_EVENTS, payload: schedule })
        })

        switch (type) {
            case VIEW_DAYS:
            value = value || Object.keys(ScheduleProps.summit.dates)[0]
            ScheduleApi.getEventByDay(summit.id, value); break
            case VIEW_TRACKS:
            value = value || Object.keys(ScheduleProps.summit.tracks)[0]
            ScheduleApi.getEventByTrack(summit.id, value); break
            case VIEW_LEVELS:
            value = value || Object.keys(ScheduleProps.summit.presentation_levels)[0].toLowerCase()
            ScheduleApi.getEventByLevel(summit.id, value); break
        }

        dispatch({ type: CHANGE_VIEW,
            payload: { view: { type, value } }
        })
    }
}

export const loadEventDetail = eventId => {
    return dispatch => {
        // type: UPDATE_EVENT,
        // payload: new Promise((resolve, reject) => {
        const url = `${ScheduleProps.base_url}events/${eventId}/html`

        $.ajax({ type: 'GET', url: url, timeout: 60000, cache: false})
        .done(detail => dispatch({
            type: UPDATE_EVENT,
            payload: {
                eventId,
                mutator: event => ({ ...event,
                    [EVENT_FIELD_DETAIL]: detail
                })
            }
        }))
        .fail(error => dispatch({
            type: UPDATE_EVENT,
            payload: {
                eventId,
                mutator: event => ({ ...event,
                    [EVENT_FIELD_EXPANDED]: false,
                    [EVENT_FIELD_LOADED]: false,
                })
            }
        }))
    }
}

export const toggleEventDetail = event => {
    return (dispatch, getState) => {

        if ( ! event[EVENT_FIELD_LOADED]) {
            event[EVENT_FIELD_LOADED] = true
            dispatch(loadEventDetail(event.id))
        }

        dispatch({
            type: UPDATE_EVENT,
            payload: {
                eventId: event.id,
                mutator: event => ({ ...event,
                    [EVENT_FIELD_EXPANDED]: ! event[EVENT_FIELD_EXPANDED],
                })
            }
        })
    }
}

export const addEventToRsvp = event => {
    if (event.rsvp_external) {
        return addEventToSchedule(event);
    }

    // our custom one, just navigate
    const url = new URI(event.rsvp_link);
    $(window).url_fragment('setParam','eventId', event.id);
    window.location.hash = $(window).url_fragment('serialize');
    url.addQuery('BackURL', window.location);
    window.location = url.toString();

    return {
        type: UPDATE_EVENT,
        payload: {
            eventId: event.id,
            mutator: event => ({ ...event, going: true })
        }
    }
}

export const removeEventFromRsvp = event => {
    ScheduleApi.unRSVPEvent(ScheduleProps.summit.id, event.id);

    return {
        type: UPDATE_EVENT,
        payload: {
            eventId: event.id,
            mutator: event => ({ ...event, going: false })
        }
    }

}

export const addEventToSchedule = event => {

    if (event.has_rsvp && event.rsvp_external) {
        // Redicrect to event's RSVP link after schedule add.
        ScheduleApi.one('addedEvent2MySchedule', (event => {
            const url = new URI(event.rsvp_link);
            url.addQuery('BackURL', window.location);
            window.location = url.toString();
        }).bind(this, event));
    }

    ScheduleApi.addEvent2MySchedule(ScheduleProps.summit.id, event.id);

    return {
        type: UPDATE_EVENT,
        payload: {
            eventId: event.id,
            mutator: event => ({ ...event, going: true })
        }
    }
}

export const removeEventFromSchedule = event => {
    ScheduleApi.removeEventFromMySchedule(ScheduleProps.summit.id, event.id);
    return {
        type: UPDATE_EVENT,
        payload: {
            eventId: event.id,
            mutator: event => ({ ...event, going: false })
        }
    }
}

export const addEventToFavorites = event => {
    ScheduleApi.addEvent2MyFavorites(ScheduleProps.summit.id, event.id);
    return {
        type: UPDATE_EVENT,
        payload: {
            eventId: event.id,
            mutator: event => ({ ...event, favorite: true })
        }
    }
}

export const removeEventFromFavorites = event => {
    ScheduleApi.removeEventFromMyFavorites(ScheduleProps.summit.id, event.id);
    return {
        type: UPDATE_EVENT,
        payload: {
            eventId: event.id,
            mutator: event => ({ ...event, favorite: false })
        }
    }
}

export const bulkSyncToggleAll = checked => {
    return {
        type: BULK_SYNC_TOGGLE_ALL,
        payload: { checked }
    }
}

export const bulkSyncToggleEvent = (event, checked) => {
    return {
        type: BULK_SYNC_TOGGLE_EVENT,
        payload: { event, checked },
    }
}

export const syncEventsToCalendar = (events, sync) => {
    return dispatch => {
        calendarPush(events, sync).then(payload => {
            dispatch({
                type: CALENDAR_SYNC,
                payload
            })
        })
    }
}

// Helper functions.
const getUrlParam = param => {
    return $(window).url_fragment('getParam', param)
}

const getAutoloadEventId = (events, view) => {
    const selectedId = parseInt(
        $(window).url_fragment('getParam','eventid')
    )

    // URL eventid parameter takes precedence.
    if (selectedId) return selectedId

    // Skip checks for other view groupings.
    if ( ! view.type === VIEW_DAYS) return 0

    const today = new Date();
    const day = new Date(view.value.split('-'))

    // Skip checks for other days.
    if (today.toDateString() !== day.toDateString()) return 0

    // Find current event.
    const epochNow = Date.now();

    const matched = events.filter(event => {
        return event.start_epoch >= epochNow / 1000
    }).shift()

    return matched ? matched.id : 0
}

const calendarPush = (events, sync) => {
    // Promise resolves after updating *all* events.
    return new Promise((resolve, reject) => {
        calendarAuth().then(() => {
            const updated = []
            const calendarMethod = sync ? 'addEvents' : 'removeEvents'
            const apiMethod = sync ? 'googleCalSynch' : 'googleCalUnSynch'

            $('#events-container').ajax_loader();

            GoogleCalendarApi[calendarMethod](events,(response, event) => {
                const gcal_id = sync ? response.result.id : null
                ScheduleApi[apiMethod]({ ...event, gcal_id })
                updated.push({ id: event.id, gcal_id })

                if (events.length === updated.length) {
                    $('#events-container').ajax_loader('stop')
                    resolve({ updated })
                }
            })

            // GoogleCalendarApi does not currently support promises.
            // Raise an error if overall execution takes more than
            // 500 ms for each event after 2 initial seconds.
            setTimeout(() => {
                if (events.length !== updated.length) {
                    $('#events-container').ajax_loader('stop')

                    setTimeout(() => { // Display error after loader closes.
                        sweetAlert('Oops...', CALENDAR_ERROR_MSG, 'error')
                    }, 500)

                    resolve({ updated })
                }
            }, events.length * 500 + 2000);
        })
    })
}

const calendarAuth = () => {
    return new Promise(resolve => {
        if (GoogleCalendarApi.isAuthorized()) {
            return resolve();
        }
        GoogleCalendarApi.doUserAuth(resolve);
    })
}

const getDefaultViewDay = () => {
    const { summit } = ScheduleProps

    const pad = (num, size) => {
        var s = num.toString()
        while (s.length < size) s = "0" + s
        return s
    }

    var now = new Date();
    var year = now.getUTCFullYear();
    var month = pad(now.getUTCMonth()+1,2);
    var day = pad(now.getUTCDate(),2);
    let filterDay = `${year}-${month}-${day}`

    if ( ! summit.dates[filterDay]){
        filterDay = summit.schedule_default_day
    }

    return filterDay
}
