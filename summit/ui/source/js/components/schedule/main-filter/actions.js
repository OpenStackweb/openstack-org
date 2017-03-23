import React from 'react'

const MainFilterActions = ({
    filters,
    bulkSync,
    setFilters,
    clearFilters,
    toggleFilters,
    ScheduleProps,
    exportCalendar,
    bulkSyncToggleAll,
}) => {
    const { summit: { current_user }, base_url } = ScheduleProps
    const { values, expanded } = filters

    return (
    <div className="row all-events-filter-row">
        <div className="col-md-4 col-xs-12 all-events-filter-link">
            <div className="col-filter-btn">
                <i title="" id="toggle-all-events-filters" data-placement="right"
                data-toggle="tooltip" data-original-title="Toggle Advanced Filters"
                className={`fa fa-filter ${expanded ? 'active' : ''}`}
                onClick={e => { e.preventDefault(); toggleFilters() }} />
            </div>
            <div className="col-filter-title">
                <span>Schedule&nbsp;Filters</span>
                {expanded &&
                <a id="clear-filters" onClick={clearFilters} style={{display: 'initial'}}>
                    CLEAR&nbsp;FILTERS&nbsp;<i className="fa fa-times"></i>
                </a>
                }
            </div>
        </div>
        <div className="col-md-7 col-xs-12">
            <div className="col-view-all-schedule">
                {values.going &&
                <form action={`${base_url}mine/`} method="POST">
                    <input type="hidden" name="goback" value="1" />
                    <button type="submit" className="btn btn-default view-all-schedule">
                        View&nbsp;/&nbsp;Print&nbsp;My&nbsp;Schedule
                    </button>
                </form>
                }
                {!values.going &&
                <form action={`${base_url}full/`} method="POST">
                    <input type="hidden" name="goback" value="1" />
                    <button type="submit" className="btn btn-default view-all-schedule">
                        View&nbsp;/&nbsp;Print&nbsp;Full&nbsp;Schedule
                    </button>
                </form>
                }
            </div>
            {values.going &&
            <div>
                <div className="col-select-all-calendar-own">
                    <input type="checkbox" id="chk_select_all" title="Select/Unselect All Events"
                    onClick={e => bulkSyncToggleAll(e.target.checked)} />
                </div>
                <div className="col-sync-calendar-own">
                    <div className="btn-group">
                        <button type="button" className="btn btn-default">
                            Sync to Calendar
                        </button>
                        <button type="button" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" className="btn btn-default dropdown-toggle">
                            <span className="caret"></span>
                            <span className="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul className="dropdown-menu">
                            <li>
                                <a data-target="#" id="link_google_sync" className="link-google-sync"
                                onClick={() => bulkSync(true)}>
                                    <span aria-hidden="true" className="glyphicon glyphicon-refresh"></span>
                                    &nbsp;Google&nbsp;Sync
                                </a>
                            </li>
                            <li>
                                <a data-target="#" id="link_google_unsync" className="link-google-unsync"
                                onClick={() => bulkSync(false)}>
                                    <i className="fa fa-calendar-times-o" aria-hidden="true"></i>
                                    &nbsp;Google&nbsp;Unsync
                                </a>
                            </li>
                            <li role="separator" className="divider"></li>
                            <li>
                                <a data-target="#" id="link_export_ics" className="link-export-ics"
                                onClick={exportCalendar}>
                                    <span className="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
                                    &nbsp;Export&nbsp;ICS
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            }
            <div className="col-switch-schedule">
                {current_user && current_user.is_attendee && ! values.favorites &&
                <button type="button" className="btn btn-primary pull-right switch_schedule"
                onClick={() => setFilters({ going: ! values.going })}>
                    <span className="glyphicon glyphicon-calendar"></span>
                    &nbsp;
                    <span className="content">
                        {values.going ? 'Full Schedule' : 'My Schedule' }
                    </span>
                </button>
                }
            </div>
            <div className="col-switch-watchlist">
                {current_user && ! values.going &&
                <button type="button" className="btn btn-primary pull-right switch_favorites"
                onClick={() => setFilters({ favorites: ! values.favorites })}>
                    <span className="glyphicon glyphicon-bookmark"></span>
                    &nbsp;
                    <span className="content">
                        {values.favorites ? 'Full Schedule' : 'Watch Later'}
                    </span>
                </button>
                }
            </div>
        </div>
    </div>
)}

MainFilterActions.propTypes = {
    filters: React.PropTypes.object.isRequired,
    bulkSync: React.PropTypes.func.isRequired,
    setFilters: React.PropTypes.func.isRequired,
    clearFilters: React.PropTypes.func.isRequired,
    toggleFilters: React.PropTypes.func.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired,
    exportCalendar: React.PropTypes.func.isRequired,
    bulkSyncToggleAll: React.PropTypes.func.isRequired,
}

export default MainFilterActions
