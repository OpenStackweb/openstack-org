import React, { Component } from 'react'
import { connect } from 'react-redux'
import {
    ScheduleProps,
    changeView,
    loadFilters,
    autoloadEvent,
} from '../actions'

import ScheduleMainView, { VIEW_DAYS, VIEW_TRACKS, VIEW_LEVELS } from './schedule/main-view'
import ScheduleEventList from './schedule/event-list'
import ScheduleMainFilter from './schedule/main-filter'

class ScheduleGrid extends Component {

    constructor(props) {
        super(props)

        ScheduleProps.month = props.month
        ScheduleProps.summit = props.summit
        ScheduleProps.base_url = props.base_url
        ScheduleProps.search_url = props.search_url
    }

    componentDidMount() {
        const { summit, loadFilters } = this.props

        if ( ! ('ontouchstart' in window)) {
            $('[data-toggle="tooltip"]').tooltip();
        }

        this.loadFacebookSdk(summit.share_info.fb_app_id)

        loadFilters() // Load filters from URL and trigger view change
    }

    componentDidUpdate(prevProps, prevState) {
        const { view, events, filtered, autoloadEvent } = this.props

        // Autoload only once after events are loaded.
        if ( ! this.autoloaded && events.length) {
            this.autoloaded = true; autoloadEvent(events, view, filtered)
        }
    }

    render() {
        const {view, events, filtered, changeView } = this.props
        return (
        <div>
            <ScheduleMainView
            view={view}
            changeView={changeView}
            ScheduleProps={ScheduleProps} />

            <div id="events-container">
                <ScheduleMainFilter
                ScheduleProps={ScheduleProps} />

                <ScheduleEventList
                events={events}
                filtered={filtered}
                ScheduleProps={ScheduleProps} />
            </div>
        </div>
        )
    }

    loadFacebookSdk(appId) {
        window.fbAsyncInit = function() {
            FB.init({
                appId: appId,
                xfbml: true,
                status: true,
                version : 'v2.7'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }
}

ScheduleGrid.propTypes = {
    month: React.PropTypes.string.isRequired,
    summit: React.PropTypes.object.isRequired,
    base_url: React.PropTypes.string.isRequired,
    search_url: React.PropTypes.string.isRequired,
    schedule_api: React.PropTypes.object.isRequired,
    schedule_filters: React.PropTypes.object.isRequired,
    default_event_color: React.PropTypes.string.isRequired,
}

function mapStateToProps(state) {
    return {
        view: state.schedule.view,
        events: state.schedule.events,
        filtered: state.schedule.filtered,
    }
}

export default connect(mapStateToProps, {
    changeView,
    loadFilters,
    autoloadEvent,
})(ScheduleGrid)
