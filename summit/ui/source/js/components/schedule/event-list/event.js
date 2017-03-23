import React, { PureComponent } from 'react'
import { connect } from 'react-redux'
import {
    EVENT_FIELD_DETAIL,
    EVENT_FIELD_EXPANDED,
    addEventToRsvp,
    loadEventDetail,
    toggleEventDetail,
    addEventToSchedule,
    addEventToFavorites,
    bulkSyncToggleEvent,
    removeEventFromRsvp,
    syncEventsToCalendar,
    removeEventFromSchedule,
    removeEventFromFavorites
} from '../../../actions'

import EventContent from './event/content'
import EventActions from './event/actions'

class Event extends PureComponent {

    shouldComponentUpdate(nextProps, nextState) {
        if (nextProps.filters === this.props.filters) {
            return true // Event changed, update.
        }

        // We need to update the event icon depending on which filter
        // is active but also want to skip unnecessary updates.
        // So only update if the *going* or *favorites* filter
        // values changed and the event is scheduled.

        return nextProps.event.going && (
            nextProps.filters.values.going !== this.props.filters.values.going ||
            nextProps.filters.values.favorites !== this.props.filters.values.favorites
        )
    }

    render() {
        console.log('render event')

        const {
            event,
            filters,
            ScheduleProps,
            addEventToRsvp,
            toggleEventDetail,
            addEventToSchedule,
            addEventToFavorites,
            bulkSyncToggleEvent,
            removeEventFromRsvp,
            syncEventsToCalendar,
            removeEventFromSchedule,
            removeEventFromFavorites,
        } = this.props

        const { summit: { current_user } } = ScheduleProps

        const expanded = event[EVENT_FIELD_EXPANDED]
        const detail = event[EVENT_FIELD_DETAIL]

        const going = current_user && current_user.is_attendee && event.going

        return (
            <div className="col-sm-12" id={`event_${event.id}`} ref="container">
                <div className="row main-event-content row-eq-height"
                onClick={() => toggleEventDetail(event)} style={this.getEventStyle()}>

                    <EventContent event={event}
                    ScheduleProps={ScheduleProps}
                    bulkSyncToggleEvent={bulkSyncToggleEvent}
                    syncEventsToCalendar={syncEventsToCalendar} />

                    <div className="event-state col-sm-1 col-xs-1">
                        {going && ! filters.values.favorites &&
                            <i className="fa fa-check-circle going-status event-status" />
                        }
                        {event.favorite && ( ! going || filters.values.favorites) &&
                            <i className="fa fa-bookmark favorite-status event-status" />
                        }
                    </div>

                    <EventActions event={event}
                    ScheduleProps={ScheduleProps}
                    addEventToRsvp={addEventToRsvp}
                    addEventToSchedule={addEventToSchedule}
                    addEventToFavorites={addEventToFavorites}
                    removeEventFromRsvp={removeEventFromRsvp}
                    removeEventFromSchedule={removeEventFromSchedule}
                    removeEventFromFavorites={removeEventFromFavorites} />
                </div>
                <div className={`row event-details ${expanded ? '' : 'hidden'}`}>
                    {detail && <div dangerouslySetInnerHTML={{__html: detail}}></div>}
                    {! detail && <i className="fa fa-spinner fa-spin"></i>}
                </div>
            </div>
        )
    }

    getEventStyle() {
        const { event, ScheduleProps: { summit } } = this.props

        const type = event.category_group_ids.length
            ? summit.category_groups[event.category_group_ids[0]]
            : summit.event_types[event.type_id]

        return { borderLeft: `3px solid ${type.color}` }
    }
}

Event.propTypes = {
    index: React.PropTypes.number.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired,
}

function mapStateToProps(state, ownProps) {
    return {
        event: state.schedule.events[ownProps.index],
        filters: state.schedule.filters,
    }
}

export default connect(mapStateToProps, {
    addEventToRsvp,
    loadEventDetail,
    toggleEventDetail,
    addEventToSchedule,
    addEventToFavorites,
    bulkSyncToggleEvent,
    removeEventFromRsvp,
    syncEventsToCalendar,
    removeEventFromSchedule,
    removeEventFromFavorites,
})(Event)
