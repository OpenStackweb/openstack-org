import React from 'react';
import { connect } from 'react-redux';
import { fetchAll, fetchOrderedItems } from './actions';
import Message from "~core-components/message";
import { AjaxLoader } from '~core-components/ajaxloader';

const SortDirectionAsc  = 'ASC';
const SortDirectionDesc = 'DESC';

class FilterSelector extends React.Component{

    render() {
        let options                             = [];
        let {onChange, className, value} = this.props;

        this.props.items.map((item,i) => {
            options.push(<option key={i} value={item.name.toLowerCase()}>{item.name}</option>);
        });

        return (
            <select value={value} className={className} onChange={onChange}>
                <option value="all">All</option>
                {options}
            </select>
        );
    }
}

class MarketplaceDriverApp extends React.Component
{
    constructor(props) {
        super(props);

        let hash = $(window).url_fragment('getParams');
        let project_filter = 'all', release_filter = 'all', vendor_filter = 'all';

        if(!$.isEmptyObject(hash)) {
            if (('project' in hash) && hash['project']) {
                project_filter = hash['project'];
            }
            if (('vendor' in hash) && hash['vendor']) {
                vendor_filter = hash['vendor'];
            }
            if (('release' in hash) && hash['release']) {
                release_filter = hash['release'];
            }
        }

        this.state = {
            sort_direction  : SortDirectionAsc,
            sort_field      : 'Project',
            project         : project_filter,
            release         : release_filter,
            vendor          : vendor_filter,
            loading         : true
        }
    }

    componentDidMount(){
        if(!this.props.items.length) {
            this.props.fetchItems(this.state.project, this.state.release, this.state.vendor, this.state.sort_field, this.state.sort_direction);
        }
    }

    onChangeSorting(e, property, items){
        e.preventDefault();
        let sort_direction = this.state.sort_direction == SortDirectionAsc ? SortDirectionDesc : SortDirectionAsc;

        this.setState({
            ...this.state,
            sort_field: property,
            sort_direction: sort_direction
        });

        this.props.reorderItems(items, property, sort_direction);
    }

    onFilterChange(e, filter){
        let target = e.currentTarget;
        let val    = target.value;
        let state = this.state;

        state[filter] = val;
        this.setState(state);

        $(window).url_fragment('setParam',filter, val);
        window.location.hash = $(window).url_fragment('serialize');

        this.props.fetchItems(this.state.project, this.state.release, this.state.vendor, this.state.sort_field, this.state.sort_direction);
    }

    render() {
        let sortProject = '';
        if(this.state.sort_field == 'Project'){
            sortProject = <span className={ this.state.sort_direction == SortDirectionAsc ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down' } aria-hidden="true"></span>
        }

        let sortVendor = '';
        if(this.state.sort_field == 'Vendor'){
            sortVendor = <span className={ this.state.sort_direction == SortDirectionAsc ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down' } aria-hidden="true"></span>
        }

        let sortDriver = '';
        if(this.state.sort_field == 'Driver'){
            sortDriver = <span className={ this.state.sort_direction == SortDirectionAsc ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down' } aria-hidden="true"></span>
        }

        let {projects, releases, vendors} = this.props.filters;
        let {items} = this.props;

        return (
            <div>
                <Message />
                <AjaxLoader show={this.props.loading} size={ 75 } />
                <div className="row" style={{ marginBottom: "10px"}}>
                    <div className="col-md-4">
                        <label>Project</label>
                        <FilterSelector className="form-control" items={projects} value={this.state.project} onChange={(e) => this.onFilterChange(e, 'project')} />
                    </div>
                    <div className="col-md-4">
                        <label>Vendor</label>
                        <FilterSelector className="form-control" items={vendors} value={this.state.vendor} onChange={(e) => this.onFilterChange(e, 'vendor')} />
                    </div>
                    <div className="col-md-4">
                        <label>Release</label>
                        <FilterSelector className="form-control" items={releases} value={this.state.release} onChange={(e) => this.onFilterChange(e, 'release')} />
                    </div>
                </div>
                {items.length > 0 &&
                    <table className="table">
                        <thead>
                            <tr>
                                <th width="10%">
                                    <a title="Order by Project" onClick={(e) => this.onChangeSorting(e, 'Project', items)} href="#">
                                    { sortProject }&nbsp;Project
                                    </a>
                                </th>
                                <th width="10%">
                                    <a title="Order by Vendor" onClick={(e) => this.onChangeSorting(e, 'Vendor', items)} href="#">
                                    { sortVendor }&nbsp;Vendor
                                    </a>
                                </th>
                                <th>
                                    <a title="Order by Driver" onClick={(e) => this.onChangeSorting(e, 'Name', items)} href="#">
                                    { sortDriver }&nbsp;Driver
                                    </a>
                                </th>
                                <th>Ships with Openstack</th>
                            </tr>
                        </thead>
                        <tbody>
                        {
                            items.map
                            (
                                driver =>

                                <tr key={driver.id}>
                                    <td>
                                        {driver.project}
                                    </td>
                                    <td>
                                        {driver.vendor}
                                    </td>
                                    <td>
                                        <a href={driver.url}>{driver.name}</a>
                                        <p>{driver.description}</p>
                                    </td>
                                    <td>
                                        {driver.releases.map((r,i) =>
                                            <a key={i+'_'+r.id} href={r.url}> {r.name + ' '} </a>
                                        )}
                                    </td>
                                </tr>
                            )
                        }
                        </tbody>
                    </table>
                }

                {items.length == 0 && <p>There are no results for these filters</p>}
            </div>
        );
    }
}

export default connect (
    state => {
        return {
            items:      state.items,
            loading:    state.loading
        }
    },
    dispatch => ({
        fetchItems (project = 'all', release = 'all', vendor = 'all', sort_field = 'Project', sort_dir = SortDirectionAsc) {
            return dispatch(fetchAll({project, release, vendor, sort_field, sort_dir}));
        },
        reorderItems (items, sort_field, sort_dir) {
            return dispatch(fetchOrderedItems({items, sort_field, sort_dir}));
        }
    })
)(MarketplaceDriverApp);
