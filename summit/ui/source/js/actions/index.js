import { getRequest, putRequest, createAction, deleteRequest, postRequest } from "~core-utils/actions";
import URI from "urijs";

export const ScheduleProps = {
    month: null,
    summit: {},
    base_url: null,
    search_url: null,
}

// Reducers
export const CHANGE_VIEW = 'CHANGE_VIEW';
export const SET_FILTERS = 'SET_FILTERS';
export const UPDATE_EVENT = 'UPDATE_EVENT';
export const TOGGLE_FILTERS = 'TOGGLE_FILTERS';
export const RECEIVE_EVENTS = 'RECEIVE_EVENTS';
export const RECEIVE_EVENTS_FULL = 'RECEIVE_EVENTS_FULL';
export const REQUESTING_EVENTS = 'REQUESTING_EVENTS';
export const UNSYNC_CALENDAR = 'UNSYNC_CALENDAR';
export const CALENDAR_SYNCD = 'CALENDAR_SYNCD';
export const SUBMITING_NEW_COMMENT = 'SUBMITING_NEW_COMMENT';
export const SUBMITTED_NEW_COMMENT = 'SUBMITTED_NEW_COMMENT';

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

export const DEFAULT_VIEWS = {
    [VIEW_DAYS]: null,
    [VIEW_TRACKS]: null,
    [VIEW_LEVELS]: null,
}

export const VIEW_DAYS   = 'day';
export const VIEW_TRACKS = 'track';
export const VIEW_LEVELS = 'level';
export const EVENT_FIELD_DETAIL = '_detail';
export const EVENT_FIELD_LOADED = '_loaded';
export const EVENT_FIELD_EXPANDED = '_expanded';

const CALENDAR_ERROR_MSG = 'Some events failed to update.';
const API_BASE_URL   = 'api/v1/summits/@SUMMIT_ID/schedule';

// Redux Action creators.
export const loadFilters = () => {
    return dispatch => {
        let view = null
        let values = {}
        const { summit } = ScheduleProps
        let cal_sync = summit.current_user ? summit.current_user.cal_sync : false;

        Object.keys(DEFAULT_FILTERS).forEach(filterName => {
            const value = getUrlParam(filterName);
            if (value) values[filterName] = value.split(',')
        });

        Object.keys(DEFAULT_VIEWS).forEach(type => {
            const value = getUrlParam(type)
            if (value) view = { type, value }
        });

        view = view || { type: VIEW_DAYS, value: getDefaultViewDay() }

        dispatch(changeView(view.type, view.value))
        dispatch(setFilters(values, Object.keys(values).length > 0, cal_sync ))
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
        ).shift();

        if (event) {
            $('html, body').animate({
                scrollTop: $(`#event_${event.id}`).offset().top
            }, 1000).promise().then(() => {
                if(hasEventIdParam()) dispatch(toggleEventDetail(event))
            })
        }
    }
}

export const setFilters = (values, expanded, calSync) => {
    return {
        type: SET_FILTERS,
        payload: { values, expanded, calSync }
    }
}

export const clearFilters = () => {
    const { summit } = ScheduleProps;
    let cal_sync = (summit.current_user) ? summit.current_user.cal_sync : false;
    return setFilters({ ...DEFAULT_FILTERS }, false, cal_sync);
}

export const toggleFilters = () => {
    return { type: TOGGLE_FILTERS }
}

export const changeView = (type, value) => {
    return dispatch => {
        const { summit } = ScheduleProps;
        switch (type) {
            case VIEW_DAYS:
            value = value || Object.keys(ScheduleProps.summit.dates)[0];
            loadEventsByDay(summit.id, value)(dispatch);
            break
            case VIEW_TRACKS:
            value = value || Object.keys(ScheduleProps.summit.tracks)[0];
            loadEventsByTrack(summit.id, value)(dispatch);
            break;
            case VIEW_LEVELS:
            value = value || Object.keys(ScheduleProps.summit.presentation_levels)[0].toLowerCase();
            loadEventsByLevel(summit.id, value)(dispatch);
            break;
        }
        dispatch({ type: CHANGE_VIEW,
            payload: { view: { type, value } }
        });
    }
}

const loadEventsByDay = (summitId, day) => (dispatch) => {
    return getRequest(
        createAction(REQUESTING_EVENTS),
        createAction(RECEIVE_EVENTS),
        `api/v1/summits/${summitId}/schedule`,
        errorHandler
    )({ day})(dispatch);
}

const loadEventsByTrack = (summitId, track) => (dispatch) => {
    return getRequest(
        createAction(REQUESTING_EVENTS),
        createAction(RECEIVE_EVENTS),
        `api/v1/summits/${summitId}/schedule/track`,
        errorHandler
    )({ track})(dispatch);
}

const loadEventsByLevel = (summitId, level) => (dispatch) => {
    return getRequest(
        createAction(REQUESTING_EVENTS),
        createAction(RECEIVE_EVENTS),
        `api/v1/summits/${summitId}/schedule/level`,
        errorHandler
    )({ level})(dispatch);
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

export const toggleScheduleState = (event) => (dispatch, getState) => {
    return (event.going) ? removeEventFromSchedule(event)(dispatch) : addEventToSchedule(event)(dispatch);
}

export const toggleFavoriteState = (event) => (dispatch, getState) =>  {
    return (event.favorite) ? removeEventFromFavorites(event)(dispatch) : addEventToFavorites(event)(dispatch);
}

export const toggleRSVPState = (event) => (dispatch, getState) =>  {
    return dispatch(createAction(UPDATE_EVENT)(
        {
            eventId: event.id,
            mutator: event => ({ ...event, going: !event.going })
        }
    ));
}

export const postNewComment = (comment) => (dispatch, getState) =>  {
   return postRequest(
       createAction(SUBMITING_NEW_COMMENT),
       createAction(SUBMITTED_NEW_COMMENT)(comment),
       `api/v1/summits/${comment.summit_id}/schedule/${comment.event_id}/feedback`,
       comment,
       errorHandler,
   )()(dispatch);
}

export const removeEventFromRsvp = (event) =>  (dispatch) => {
    return deleteRequest(
        null,
        createAction(UPDATE_EVENT)(
            {
                eventId: event.id,
                mutator: event => ({ ...event, going: false })
            }
        ),
        `api/v1/summits/${event.summit_id}/schedule/${event.id}/rsvp`,
        {},
        errorHandler,
    )()(dispatch);
}

export const addEventToSchedule = (event) => (dispatch) => {
    return putRequest(
        null,
        createAction(UPDATE_EVENT)(
            {
                eventId: event.id,
                mutator: event => ({ ...event, going: true })
            }
        ),
        `api/v1/summits/${event.summit_id}/schedule/${event.id}`,
        {},
        errorHandler,
    )()(dispatch)
    .then(
            () => {
                console.log(`calling then action for ${event.id}`);
                if (event.has_rsvp && event.rsvp_external){
                    const url = new URI(event.rsvp_link);
                    url.addQuery('BackURL', window.location);
                    window.location = url.toString();
                }
            }
    );
}

export const removeEventFromSchedule = (event) => (dispatch) =>  {
    return deleteRequest(
        null,
        createAction(UPDATE_EVENT)(
            {
                eventId: event.id,
                mutator: event => ({ ...event, going: false })
            }
        ),
        `api/v1/summits/${event.summit_id}/schedule/${event.id}`,
        {},
        errorHandler,
    )()(dispatch);
}

export const addEventToFavorites = (event) => (dispatch) => {
    return putRequest(
        null,
        createAction(UPDATE_EVENT)(
            {
                eventId: event.id,
                mutator: event => ({ ...event, favorite: true })
            }
        ),
        `api/v1/summits/${event.summit_id}/schedule/${event.id}/favorite`,
        {},
        errorHandler,
    )()(dispatch);
}

export const removeEventFromFavorites = event => dispatch => {
    return deleteRequest(
        null,
        createAction(UPDATE_EVENT)(
            {
                eventId: event.id,
                mutator: event => ({ ...event, favorite: false })
            }
        ),
        `api/v1/summits/${event.summit_id}/schedule/${event.id}/favorite`,
        {},
        errorHandler,
    )()(dispatch);
}

// Helper functions.
const getUrlParam = param => {
    return $(window).url_fragment('getParam', param)
}

const hasEventIdParam = () => {
    const selectedId = parseInt(
        $(window).url_fragment('getParam','eventid')
    );
    return selectedId > 0;
}

const pad = (num, size) => {
    let s = num.toString();
    while (s.length < size) s = "0" + s;
    return s;
};

const getCurrentDayLabel = () => {
    let timestamp      = new Date().getTime();
    let offset         = summit.offset * 1000;
    let now            = new Date(timestamp + offset);
    let year           = now.getUTCFullYear();
    let month          = pad(now.getUTCMonth()+1,2);
    let day            = pad(now.getUTCDate(),2);
    return `${year}-${month}-${day}`;
}

const getLocalNow = () =>{
    let now = Math.ceil(Date.now()) / 1000;
    return now + ScheduleProps.summit;
}

const getAutoloadEventId = (events, view) => {
    const selectedId = parseInt(
        $(window).url_fragment('getParam','eventid')
    )

    // URL eventid parameter takes precedence.
    if (selectedId) return selectedId;

    // Skip checks for other view groupings.
    if ( ! view.type === VIEW_DAYS) return 0;

    // Skip checks for other days.
    if (getCurrentDayLabel() !== view.value) return 0;

    // Find current event.
    const now = getLocalNow();

    let candidateEndDate = null;
    const matched = events.filter(event => {
        if(event.class_name !== 'Presentation') return false;
        let alreadyStarted = event.start_epoch <= now;
        // if already started and started more than hour ago skip it
        if(alreadyStarted && (now - event.start_epoch) > 3600 ) return false;
        let elegible = event.end_epoch >= now;
        if(elegible && (candidateEndDate == null || candidateEndDate > event.end_epoch)){
            candidateEndDate = event.end_epoch;
            return true;
        }
        return false;
    }).shift();

    return matched ? matched.id : 0
}

const getDefaultViewDay = () => {
    const { summit } = ScheduleProps;
    // try to get from fragment
    let filterDay = getUrlParam('day');
    if(filterDay == null ) // get current day
        filterDay = getCurrentDayLabel();
    console.log(`filterDay ${filterDay}`);

    if (!summit.dates[filterDay]){
        filterDay = summit.schedule_default_day;
    }

    return filterDay;
}

const errorHandler = (err, res) => {
    let text = res.body;
    if(res.body != null && res.body.messages instanceof Array) {
        let messages = res.body.messages.map( m => {
            if (m instanceof Object) return m.message
            else return m;
        })
        text = messages.join('\n');
    }

    sweetAlert(res.statusText, text, "error");
}

export const unSyncCalendar = () => {
        let summit_id = ScheduleProps.summit.id;
        return deleteRequest(
            createAction(''),
            createAction(UNSYNC_CALENDAR),
            `summit-calendar-sync/unsync?summit_id=${summit_id}`,
            {},
            errorHandler
        )();
}

export const syncCalendar = (cal_type, ios_user, ios_pass ) => (dispatch) => {
    let summit_id = ScheduleProps.summit.id;
    console.log(`syncCalendar - cal_type ${cal_type}`);
    switch (cal_type) {
        case 'google':
            window.location = `summit-calendar-sync/login-google?state=${summit_id}`;
            break;
        case 'apple':
             return putRequest(
                createAction(''),
                createAction(CALENDAR_SYNCD),
                 'summit-calendar-sync/login-apple',
                {ios_user: ios_user, ios_pass: ios_pass},
                errorHandler
            )({state : summit_id})(dispatch);
            break;
        case 'outlook':
            window.location = `summit-calendar-sync/login-outlook?state=${summit_id}`;
            break;
    }
}

export const pullScheduleByViewType = (viewType, summitId) => (dispatch) => {
    return getRequest(
        createAction(REQUESTING_EVENTS),
        createAction(RECEIVE_EVENTS_FULL),
        `api/v1/summits/${summitId}/schedule/full`,
        errorHandler
    )({sort:viewType})(dispatch);
}