import React from 'react'
import Event from './event-list/event'

const NO_EVENTS_MSG = `* The combination of filters you have selected
    resulted in no matching events. Please adjust the filters or try
    different search parameters.`

const EventList = ({
    events,
    filtered,
    ScheduleProps,
}) => {
    console.log('render list')

    const noResults = filtered.length && filtered.length === events.length

    const getHiddenClass = event => {
        return filtered.indexOf(event.id) >= 0 ? 'hide' : ''
    }

    return (
        <div className="row">
            <div className={`col-md-12 col-xs-12 ${noResults ? 'hide' : ''}`}>
                {events.map((event, index) => (
                <div key={event.id} id={`event_${event.id}`}
                className={`row event-row ${getHiddenClass(event)}`}>
                    <Event index={index} ScheduleProps={ScheduleProps} />
                </div>
                ))}
            </div>
            <div className={`col-md-12 col-xs-12 ${noResults ? '' : 'hide'}`}>
                <p>{NO_EVENTS_MSG}</p>
            </div>
        </div>
    )
}

EventList.propTypes = {
    events: React.PropTypes.array.isRequired,
    filtered: React.PropTypes.array.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired,
};

export default EventList
