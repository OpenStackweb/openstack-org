import React, { Component } from 'react'
import { connect } from 'react-redux'
import {
    setFilters,
    clearFilters,
    toggleFilters,
    bulkSyncToggleAll,
    syncEventsToCalendar,
} from '../../actions'

const CALENDAR_NOT_SYNC_MSG = 'You selected one or more events that are not synched!';
const CALENDAR_NO_EVENTS_MSG = 'You must select at least one event!';
const CALENDAR_ALREADY_SYNC_MSG = 'You selected one or more events that are already synched!';

import MainFilterFields from './main-filter/fields'
import MainFilterActions from './main-filter/actions'

const RSVP_NOTE_TEXT = `Please note that adding an item to "My Schedule"
    does not guarantee a seat in the presentation. Rooms fill up fast,
    so get there early. Some events require an RSVP and, in those cases,
    you will see a link to RSVP to the event.`

class MainFilter extends Component {

    constructor(props) {
        super(props)

        this.bulkSync = this.bulkSync.bind(this)
        this.exportCalendar = this.exportCalendar.bind(this)
    }

    render() {
        const {
            filters,
            setFilters,
            clearFilters,
            toggleFilters,
            ScheduleProps,
            exportCalendar,
            bulkSyncToggleAll,
        } = this.props

        const { summit: { current_user } } = ScheduleProps

        return (
            <div>
                <MainFilterActions
                filters={filters}
                bulkSync={this.bulkSync}
                setFilters={setFilters}
                clearFilters={clearFilters}
                toggleFilters={toggleFilters}
                ScheduleProps={ScheduleProps}
                exportCalendar={this.exportCalendar}
                bulkSyncToggleAll={bulkSyncToggleAll} />

                {current_user &&
                <div className="rsvp-note">{RSVP_NOTE_TEXT}</div>
                }

                <MainFilterFields
                filters={filters}
                setFilters={setFilters}
                ScheduleProps={ScheduleProps} />
            </div>
        )
    }

    bulkSync(sync) {
        const { bulk, events, syncEventsToCalendar } = this.props

        if ( ! bulk.length) {
            return sweetAlert('Oops...', CALENDAR_NO_EVENTS_MSG, 'error');
        }

        const bulkEvents = events.filter(event => {
            return bulk.indexOf(event.id) >= 0
        })
        if (sync && bulkEvents.some(event => event.gcal_id)) {
            return sweetAlert('Oops...', CALENDAR_ALREADY_SYNC_MSG, 'error')
        }
        if ( ! sync && bulkEvents.some(event => ! event.gcal_id)) {
            return sweetAlert('Oops...', CALENDAR_NOT_SYNC_MSG, 'error')
        }

        syncEventsToCalendar(bulkEvents, sync)
    }

    exportCalendar() {
        const { bulk, ScheduleProps: { summit } } = this.props

        if ( ! bulk.length) {
            return sweetAlert('Oops...', CALENDAR_NO_EVENTS_MSG, 'error')
        }

        const url = `api/v1/summits/${summit.id}/schedule/export/ics?events_id=${this.props.bulk}`;
        const dim = "width=0,height=0,menubar=no,location=no,resizable=no,scrollbars=no,status=no";

        window.open(url, "", dim);
    }

}

MainFilter.propTypes = {
    ScheduleProps: React.PropTypes.object.isRequired,
}

function mapStateToProps(state) {
    return {
        bulk: state.schedule.bulk,
        events: state.schedule.events,
        filters: state.schedule.filters,
    }
}

export default connect(mapStateToProps, {
    setFilters,
    clearFilters,
    toggleFilters,
    bulkSyncToggleAll,
    syncEventsToCalendar,
})(MainFilter)
