import {
    ScheduleProps,
    CHANGE_VIEW,
    SET_FILTERS,
    UPDATE_EVENT,
    DEFAULT_VIEWS,
    CALENDAR_SYNC,
    TOGGLE_FILTERS,
    RECEIVE_EVENTS,
    BULK_SYNC_TOGGLE_ALL,
    BULK_SYNC_TOGGLE_EVENT,
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
    },
    bulk: [],
    events: [],
    filtered: [],
}

const ScheduleReducer = (state = DEFAULT_STATE, action) => {
    const { type, payload } = action
    switch (type) {

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

            // Reset bulk sync status.
            updateBulkSyncCheck(false)
            clearEventSyncChecks()

            return { ...state, view, filters, events: [], bulk: [] }


        case RECEIVE_EVENTS:
            var { events } = payload

            // Get the new list of filtered events.
            var filtered = Filter.events(
                state.filters.values, events, ScheduleProps.summit
            )

            return { ...state, events, filtered }


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

            // Build the new filter object with allowed tracks data.
            var filters = { ...state.filters, values, expanded,
                allowedTracks: getAllowedTracks(values),
            }

            if (filters.values.tracks) {
                // Remove filtered tracks depending on track_groups value.
                var tracks = filters.values.tracks.filter(trackId => {
                    return filters.allowedTracks.indexOf(parseInt(trackId)) >= 0
                })
                filters.values.tracks = tracks.length ? tracks : null
            }

            // Reset bulk sync status.
            updateBulkSyncCheck(false)
            toggleEventSyncChecks(values.going)
            clearEventSyncChecks()

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


        case CALENDAR_SYNC:
            var { updated } = payload

            if (updated.length) {
                updateBulkSyncCheck(false)
            }

            var events = state.events
            var bulk = [ ...state.bulk ]

            updated.forEach(eventData => {
                // Update event's gcal id.
                const mutator = event => ({ ...event,
                    gcal_id: eventData.gcal_id
                })
                // Replace old event.
                events = updateEvent(eventData.id, mutator, events)
                // Remove event from bulk.
                bulk.splice(bulk.indexOf(eventData.id), 1)
                // Unckeck event sync check.
                updateEventsSyncChecks([eventData.id], false)
            })

            return { ...state, events, bulk }


        case BULK_SYNC_TOGGLE_ALL:
            var { checked } = payload
            var bulk = []

            clearEventSyncChecks()
            updateBulkSyncCheck(checked)

            if (checked) {
                bulk = getVisibleEvents(state).map(event => event.id)
                updateEventsSyncChecks(bulk, true)
            }

            // DEBUG
            // console.log(bulk.map(event => event.title))

            return { ...state, bulk }


        case BULK_SYNC_TOGGLE_EVENT:
            var { event, checked } = payload
            var bulk = [ ...state.bulk ]
            var index = bulk.indexOf(event.id)

            if (checked) {
                // Add event if not exists.
                if (bulk.indexOf(event.id) < 0) {
                    bulk.push(event.id)
                }
            } else if (index >= 0) {
                // Remove event if exists.
                bulk.splice(index, 1)
                updateBulkSyncCheck(false)
            }

            updateEventsSyncChecks([event.id], checked)

            // DEBUG
            // console.log(state.events.filter(event => {
            //     return bulk.indexOf(event.id) >= 0
            // }).map(event => event.title))

            return { ...state, bulk }
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
            $(window).url_fragment('setParam', param, params[param])
        }
    })

    window.location.hash = $(window).url_fragment('serialize');
}

const updateBulkSyncCheck = checked => {
    $('#chk_select_all').prop('checked', checked);
}

const updateEventsSyncChecks = (eventIds, checked) => {
    eventIds.forEach(eventId => {updateEventsSyncChecks
        $(`#event_${eventId} .select-event-chk`).prop('checked', checked);
    })
}

const clearEventSyncChecks = () => {
    $(".select-event-chk").prop('checked', false);
}

const toggleEventSyncChecks = visible => {
    $('#events-container .synch-container')[visible ? 'show' : 'hide']();
}

export default ScheduleReducer
