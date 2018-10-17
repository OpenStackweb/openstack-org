import React from 'react';

const EventContent = ({
    event,
    ScheduleProps,
    bulkSyncToggleEvent,
    syncEventsToCalendar,
}) => {
    const { summit, search_url } = ScheduleProps

    const getSearchLink = term => {
        term = term.replace(/\s+/g, '+')
        return `${search_url}?t=${encodeURIComponent(term)}`
    }

    const location = summit.locations[event.location_id]

    const locationName = location ? location.name_nice : 'TBA'
    const venueSearchLink = location ? location.link : summit.link + 'venues'

    const trackSearchLink = event.track_id && summit.tracks[event.track_id]
        ? getSearchLink(summit.tracks[event.track_id].name)
        : '#'

    const eventTypeSearchLink = summit.event_types[event.type_id]
        ? getSearchLink(summit.event_types[event.type_id].type)
        : '#'

    return (
    <div className="event-content col-sm-10 col-xs-10">
        <div className="row row_location">
            <div className="col-sm-4 col-md-3 col-time">
                <i className="fa fa-clock-o icon-clock" />
                <span className="event-date">{event.date_nice}</span>
                ,&nbsp;
                <span className="start-time">{event.start_time}</span>
                &nbsp;-&nbsp;
                <span className="end-time">{event.end_time}</span>
            </div>
            <div className="col-sm-8 col-md-9 col-location">
                {summit.should_show_venues &&
                <div>
                    <i className="fa fa-map-marker icon-map"></i>&nbsp;
                    <a onClick={e => e.stopPropagation()} href={venueSearchLink}
                    className="search-link venue-search-link">
                        {locationName}
                    </a>
                </div>
                }
            </div>
        </div>
        <div className="row">
            <div className="col-md-10">
                <span className="event-title">{event.title}</span>
                {event.attachment_url &&
                <a onClick={e => e.stopPropagation()} href={event.attachment_url}
                className="search-link attachment-link">
                    <i className="search-link fa fa-download" />
                </a>
                }
                {event.to_record == true &&
                <span className="record-icon">
                    <i className="fa fa-video-camera" />
                </span>
                }
            </div>
        </div>
        <div className="row">
            <div className="col-xs-8 col-track">
                {event.class_name !== 'SummitEvent' &&
                <span className="track">
                    <a onClick={e => e.stopPropagation()} href={trackSearchLink}
                    className="search-link track-search-link" title="Search Track">
                        {event.track_id ? summit.tracks[event.track_id].name : ''}
                    </a>
                </span>
                }
            </div>
            <div className="col-xs-4 event-type-col">
                <a onClick={e => e.stopPropagation()} href={eventTypeSearchLink}
                className="search-link event-type-search-link" title="Search Event Type">
                    {summit.event_types[event.type_id].type }
                </a>
            </div>
        </div>
    </div>
    );
}

EventContent.propTypes = {
    event: React.PropTypes.object.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired
};

export default EventContent;
