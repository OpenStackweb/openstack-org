import {
    ScheduleProps,
    CHANGE_VIEW,
    SET_FILTERS,
    UPDATE_EVENT,
    DEFAULT_VIEWS,
    TOGGLE_FILTERS,
    RECEIVE_EVENTS,
    UNSYNC_CALENDAR,
    CALENDAR_SYNCD,
    REQUESTING_EVENTS,
    RECEIVE_EVENTS_FULL
} from '../actions'

import Filter from './tools/filter'

const FILTER_URL_EXCLUDE = ['going', 'favorites']

const DEFAULT_STATE = {
    view: {
        type: '',
        value: '',
    },
    filters: {
        values: {},
        visible: true,
        expanded: false,
        allowedTracks: [],
        calSync: false,
    },
    bulk: [],
    events: [],
    filtered: [],
    loading: false,
}

const ScheduleReducer = (state = DEFAULT_STATE, action) => {
    const { type, payload } = action
    switch (type) {
        case REQUESTING_EVENTS:
            return {
                ...state,
                loading: true,
            }
        case CHANGE_VIEW:
            var { view } = payload

            // Reset favorites and going filters only.
            var filters = { ...state.filters,
                values: { ...state.filters.values,
                    going: false,
                    favorites: false,
                }
            }

            // Write filter values to URL.
            setUrlParams({ ...DEFAULT_VIEWS,
                [view.type]: view.value
            }, FILTER_URL_EXCLUDE)

            return { ...state, view, filters, events: [], bulk: [] }

        case RECEIVE_EVENTS:
            var { events } = payload.response;

            // Get the new list of filtered events.
            var filtered = Filter.events(
                state.filters.values, events, ScheduleProps.summit
            )

            return { ...state, events, filtered, loading: false}

        case RECEIVE_EVENTS_FULL:

            return { ...state, events: payload.response, loading: false}

        case UPDATE_EVENT:
            var { eventId, mutator } = payload

            // Replace old event.
            var events = updateEvent(eventId, mutator, state.events)

            // Get the new list of filtered events.
            var filtered = Filter.events(
                state.filters.values, events, ScheduleProps.summit
            )

            // Remove any bulk event that could be now filtered.
            var bulk = state.bulk.filter(eventId => {
                return filtered.indexOf(eventId) < 0
            })

            return { ...state, events, filtered, bulk }

        case SET_FILTERS:
            var values = { ...state.filters.values, ...payload.values }

            // Use expanded payload parameter if present.
            var expanded = payload.expanded == undefined
                ? state.filters.expanded
                : payload.expanded

            var calSync = payload.calSync == undefined
                ? state.filters.calSync
                : payload.calSync

            // Build the new filter object with allowed tracks data.
            var filters = { ...state.filters, values, expanded, calSync,
                allowedTracks: getAllowedTracks(values),
            }

            if (filters.values.tracks) {
                // Remove filtered tracks depending on track_groups value.
                var tracks = filters.values.tracks.filter(trackId => {
                    return filters.allowedTracks.indexOf(parseInt(trackId)) >= 0
                })
                filters.values.tracks = tracks.length ? tracks : null
            }

            // Write filter values to URL.
            setUrlParams(filters.values, FILTER_URL_EXCLUDE)

            // Get the new list of filtered events.
            var filtered = Filter.events(
                filters.values, state.events, ScheduleProps.summit
            )

            return { ...state, filters, filtered, bulk: [] }


        case TOGGLE_FILTERS:
            return { ...state,
                filters: { ...state.filters,
                    expanded: ! state.filters.expanded
                }
            }

        case UNSYNC_CALENDAR:
            return { ...state,
                filters: { ...state.filters,
                    calSync: false
                }
            }

        case CALENDAR_SYNCD:
            window.location = ScheduleProps.summit.schedule_link;

    }

    return state
}

// Helper functions.
const updateEvent = (eventId, mutator, events) => {
    return events.map(event => {
        return event.id == eventId ? mutator(event) : event
    })
}

const getAllowedTracks = (filters) => {
    let tracks = []

    // Empty filter value means *show all* groups.
    const selectedGroups = filters.track_groups
        ? filters.track_groups
        : Object.keys(ScheduleProps.summit.category_groups)

    // Extract allowed tracks from each selected group.
    selectedGroups.forEach(groupId => {
        const categoryGroup = ScheduleProps.summit.category_groups[groupId]
        tracks = tracks.concat(categoryGroup.tracks)
    })

    return tracks
}

const getVisibleEvents = state => {
    return state.events.filter(event => {
        return state.filtered.indexOf(event.id) < 0
    })
}

const setUrlParams = (params, exclude = []) => {
    Object.keys(params).forEach(param => {
        if (exclude.indexOf(param) < 0) {
            $(window).url_fragment('setParam', param, params[param]);
        }
    })

    window.location.hash = $(window).url_fragment('serialize');
}

export default ScheduleReducer
