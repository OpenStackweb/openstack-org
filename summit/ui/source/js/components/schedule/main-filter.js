import React, { Component } from 'react';
import { connect } from 'react-redux';
import {
    setFilters,
    clearFilters,
    toggleFilters,
    createCalendarShareableLink,
    deleteCalendarShareableLink
} from '../../actions';

import MainFilterFields from './main-filter/fields';
import MainFilterActions from './main-filter/actions';
import SyncModal from './main-filter/sync_modal';
import ShareableLinkModal from './main-filter/shareable_link_modal';

const CALENDAR_NO_EVENTS_MSG = 'You must select at least one event!';


const RSVP_NOTE_TEXT = `Please note: All times below are listed in Pacific Time Zone.`;

class MainFilter extends Component {

    constructor(props) {
        super(props)

        this.state = {
            showSyncModal: false,
            showShareLinkModal: false,
            shareableLink: null,
        }

        this.toggleCalSyncClick = this.toggleCalSyncClick.bind(this);
        this.hideSyncModal = this.hideSyncModal.bind(this);
        this.getShareableLink = this.getShareableLink.bind(this);
        this.hideShareLinkModal = this.hideShareLinkModal.bind(this);
    }

    toggleCalSyncClick(e) {
        if (this.props.filters.calSync){
            this.setState({ ...this.state,
                showSyncModal: true
            });
            return;
        }
        // go to sync page
        window.location = this.props.ScheduleProps.base_url + 'sync-cal';
    }

    getShareableLink(e) {

        this.props.createCalendarShareableLink().then((response) => {
            this.setState({ ...this.state,
                showShareLinkModal: true,
                shareableLink: response.response.calendar_shareable_link
            });
        });

    }

    hideSyncModal(e) {
        this.setState({ ...this.state,
            showSyncModal: false
        });
    }

    hideShareLinkModal(e) {
        this.setState({
            ...this.state,
            showShareLinkModal: false
        });
    }

    render() {
        const {
            filters,
            setFilters,
            clearFilters,
            toggleFilters,
            ScheduleProps,
        } = this.props

        const { summit: { current_user } } = ScheduleProps

        return (
            <div>
                <MainFilterActions
                filters={filters}
                setFilters={setFilters}
                clearFilters={clearFilters}
                toggleFilters={toggleFilters}
                toggleCalSyncClick={this.toggleCalSyncClick}
                ScheduleProps={ScheduleProps}
                getShareableLink={this.getShareableLink}
               />

                {current_user &&
                <div className="rsvp-note">{RSVP_NOTE_TEXT}</div>
                }

                <MainFilterFields
                filters={filters}
                setFilters={setFilters}
                ScheduleProps={ScheduleProps} />

                <SyncModal showModal={this.state.showSyncModal}
                           hideModal={this.hideSyncModal} />

                <ShareableLinkModal showModal={this.state.showShareLinkModal}
                                    shareableLink={this.state.shareableLink}
                                    hideModal={this.hideShareLinkModal}/>
            </div>
        )
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
    createCalendarShareableLink,
    deleteCalendarShareableLink
})(MainFilter)
