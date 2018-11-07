import React, { Component } from 'react'
import { VIEW_DAYS, VIEW_TRACKS, VIEW_LEVELS } from '../../actions'

class MainView extends Component {

    constructor(props) {
        super(props)
        this.changeView = this.changeView.bind(this)
    }

    render() {
        const { view } = this.props
        return (
        <div className="row navbar-container">
            <div className="col-lg-3 col-md-2 col-sm-1 view-select-container">
                <span className="view-filter-label">Filter</span>
                <select id="view-select" value={view.type}

                onChange={e => this.changeView(e, e.target.value)}>
                    <option value={VIEW_DAYS}>Day</option>
                    <option value={VIEW_TRACKS}>Track</option>
                    <option value={VIEW_LEVELS}>Level</option>
                </select>
            </div>
            <div className="col-lg-9 col-md-10 col-sm-11 view-days-container">
            {view.type === VIEW_DAYS   && this.renderDays()}
            {view.type === VIEW_TRACKS && this.renderTracks()}
            {view.type === VIEW_LEVELS && this.renderLevels()}
            </div>
        </div>
        )
    }

    renderDays() {
        const {
            view,
            ScheduleProps: { month, summit }
        } = this.props;

        let currentValue = summit.schedule_default_day;
        for(var day of Object.keys(summit.dates)){
            if(view.value == day){
                currentValue = view.value;
                break;
            }
        }
        // check if current value belongs to summit

        return (
        <nav className="navbar navbar-default navbar-days">
            <div>
                {/* Brand and toggle get grouped for better mobile display */}
                <div className="navbar-header">
                    <button id="schedule-navbar-button" type="button" aria-expanded="false" data-toggle="collapse"
                    className="navbar-toggle collapsed" data-target="#day-navbar-colapse">
                        <span className="sr-only">
                            Toggle navigation
                        </span>
                        <span className="icon-bar"></span>
                        <span className="icon-bar"></span>
                        <span className="icon-bar"></span>
                    </button>
                    <a href="#" onClick={e => e.preventDefault()}
                    className="navbar-brand navbar-brand-month">
                        {month}
                    </a>
                </div>
                {/* Collect the nav links, forms, and other content for toggling */}
                <div className="collapse navbar-collapse" id="day-navbar-colapse">
                    <ul className="nav navbar-nav">
                    {Object.keys(summit.dates).map(dateId => {
                        const day = summit.dates[dateId]
                        return (
                        <li key={dateId} className={dateId === currentValue ? 'active day-selected' : null}>
                            <a onClick={e => this.changeView(e, view.type, dateId)}
                            href="#" className="day-label">
                                {day.label}
                            </a>
                        </li>
                    )})}
                    </ul>
                </div>
            </div>
        </nav>
        )
    }

    renderTracks() {
        const {
            view,
            ScheduleProps: { summit }
        } = this.props
        return (
        <div className="track-nav-container">
            <select value={view.value} id="track-select" ref="tracks_dropdown"
            onChange={e => this.changeView(e, view.type, e.target.value)}>
            {summit.track_ids.map(trackId => (
                <option key={trackId} value={trackId}>
                    {summit.tracks[trackId].name}
                </option>
            ))}
            </select>
        </div>
        )
    }

    renderLevels() {
        const {
            view,
            ScheduleProps: { summit }
        } = this.props
        return (
        <nav className="navbar navbar-default navbar-days">
            <div>
            {/* Brand and toggle get grouped for better mobile display */}
                <div className="navbar-header">
                    <button id="schedule-navbar-button" type="button" aria-expanded="false" data-toggle="collapse"
                    className="navbar-toggle collapsed" data-target="#level-navbar-colapse">
                        <span className="sr-only">
                        Toggle navigation
                        </span>
                        <span className="icon-bar"></span>
                        <span className="icon-bar"></span>
                        <span className="icon-bar"></span>
                    </button>
                    <a href="#" onClick={e => e.preventDefault()}
                    className="navbar-brand navbar-brand-month">
                        Levels
                    </a>
                </div>
                {/* Collect the nav links, forms, and other content for toggling */}
                <div className="collapse navbar-collapse" id="level-navbar-colapse">
                    <ul className="nav navbar-nav">
                    {Object.keys(summit.presentation_levels).map(levelId => {
                        const level = summit.presentation_levels[levelId]
                        const classes = levelId.toLowerCase() === view.value ? 'active level-selected' : null
                        return (
                            <li key={levelId} className={classes}>
                                <a onClick={e => this.changeView(e, view.type, levelId.toLowerCase())}
                                href="#" className="level-label">
                                        {level.level}
                                </a>
                            </li>
                    )})}
                    </ul>
                </div>
            </div>
        </nav>
        )
    }

    changeView(e, type, value) {
        e.preventDefault()
        this.props.changeView(type, value)
    }
}

MainView.propTypes = {
    view: React.PropTypes.object.isRequired,
    changeView: React.PropTypes.func.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired,
}

export default MainView
