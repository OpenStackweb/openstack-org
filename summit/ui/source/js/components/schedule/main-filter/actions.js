import React from 'react';
import ToggleButton from "~core-components/toggle_button";


const MainFilterActions = ({
    filters,
    setFilters,
    clearFilters,
    toggleFilters,
    toggleCalSyncClick,
    ScheduleProps
}) => {
    const { summit: { current_user }, base_url } = ScheduleProps;
    const { values, expanded, calSync }          = filters;
    let full_view_url = (values.going) ? `${base_url}mine/` : `${base_url}full/`;

    return (
    <div className="row all-events-filter-row">
        <div className="col-md-4 col-xs-12 all-events-filter-link">
            <div className="col-filter-btn">
                <i title="" id="toggle-all-events-filters" className={`fa fa-filter ${expanded ? 'active' : ''}`}
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
            <div className="col-view-all-schedule filter-button-col-left">
                <form action={full_view_url} method="POST">
                    <input type="hidden" name="goback" value="1" />
                    <button type="submit" className="btn btn-default view-all-schedule">
                    Full&nbsp;/&nbsp;Print&nbsp;View
                    </button>
                </form>
            </div>
            {current_user && ! values.favorites &&
                <div className="col-switch-schedule filter-button-col-right">
                    <button type="button" className="btn btn-primary pull-right switch_schedule"
                    onClick={() => setFilters({ going: ! values.going })}>
                        <span className="glyphicon glyphicon-calendar"></span>
                        &nbsp;
                        <span className="content">
                            {values.going ? 'Full Schedule' : 'My Schedule' }
                        </span>
                    </button>
                </div>
            }
            {current_user && ! values.going &&
                <div className="col-switch-watchlist filter-button-col-left">
                    <button type="button" className="btn btn-primary pull-right switch_favorites"
                    onClick={() => setFilters({ favorites: ! values.favorites })}>
                        <span className="glyphicon glyphicon-bookmark"></span>
                        &nbsp;
                        <span className="content">
                            {values.favorites ? 'Full Schedule' : 'Watch Later'}
                        </span>
                    </button>
                </div>
            }
            {summit.should_show_venues && current_user &&
                <div className="col-toggle-sync filter-button-col-right">
                    <ToggleButton
                        onClick={ () => toggleCalSyncClick() }
                        className="toggle_sync"
                        on={<span><span className="glyphicon glyphicon-calendar"></span>Synced</span>}
                        off={<span>Not Synced</span>}
                        offstyle="danger"
                        active={calSync}
                    />
                </div>
            }
        </div>
    </div>
)}

MainFilterActions.propTypes = {
    filters: React.PropTypes.object.isRequired,
    setFilters: React.PropTypes.func.isRequired,
    clearFilters: React.PropTypes.func.isRequired,
    toggleFilters: React.PropTypes.func.isRequired,
    toggleCalSyncClick: React.PropTypes.func.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired,
}

export default MainFilterActions
