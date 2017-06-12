import React from 'react';
import { connect } from 'react-redux';
import { fetchAllProducts, exportAllProducts } from './actions';
import { AjaxLoader } from '../../../../../ui-core/ui/source/js/components/ajaxloader';

const SortDirectionAsc  = 'ASC';
const SortDirectionDesc = 'DESC';

class FilterLink extends React.Component {

    render() {
        let {filter, currentFilter, onClick, children} = this.props;
        if (filter === currentFilter) {
            return <span>{children}</span>;
        }

        return (
            <a href='#' onClick={onClick}>
                {children}
            </a>
        );
    }
}

class SangriaOpenStackProductsByRegionApp extends React.Component
{
    constructor(props) {
        super(props);
        this.state = {
            sort_direction  : SortDirectionAsc,
            sort_field      : 'name',
            current_page: 1,
            page_size: 25,
            show_all : 1,
            region: 'ALL',
            type: 'ALL',
            search_term: '',
        }
    }

    componentWillMount(){

    }

    componentDidUpdate(){
    }

    componentDidMount(){
        if(!this.props.items.length) {
            this.props.fetchPage(this.state.current_page, this.state.page_size, this.state.show_all, this.buildSort());
        }
    }

    componentDidUpdate(prevProps, prevState){
        if
        (
            prevState.sort_direction != this.state.sort_direction ||
            prevState.sort_field != this.state.sort_field ||
            prevState.current_page != this.state.current_page ||
            prevState.region != this.state.region ||
            prevState.type != this.state.type ||
            prevState.search_term != this.state.search_term ||
            prevState.show_all != this.state.show_all ||
            prevState.page_size != this.state.page_size
        )
            this.props.fetchPage(this.state.current_page, this.state.page_size, this.state.show_all, this.buildSort(), this.state.region, this.state.type, this.state.search_term);
    }

    onChangeSorting(e, property){
        e.preventDefault();
        this.setState({...this.state, sort_field: property, sort_direction: this.state.sort_direction == SortDirectionAsc ? SortDirectionDesc : SortDirectionAsc });
        return false;
    }

    buildSort(){
        let dir = this.state.sort_direction == SortDirectionAsc ? '+':'-';
        return `${this.state.sort_field}${dir}`;
    }

    onChangePage(e){
        e.preventDefault();
        let target         = e.currentTarget;
        let current_page   = target.attributes.getNamedItem('data-page').value;
        console.log('onChangePage page '+ current_page);
        this.setState({...this.state, current_page});
    }

    onRegionFilterChange(e){
        let target = e.currentTarget;
        let val    = target.value;
        console.log(`onRegionFilterChange value ${val}`);
        this.setState({...this.state, region: val, current_page: 1});
    }

    onProductTypeFilterChange(e){
        let target = e.currentTarget;
        let val    = target.value;
        console.log(`onProductTypeFilterChange value ${val}`);
        this.setState({...this.state, type: val, current_page: 1});
    }

    onFilterByCompanyName(e){
        let target = e.currentTarget;
        let val    = target.value;
        console.log('onFilterByCompanyName value '+ val);
        this.setState({...this.state, search_term: val, current_page: 1});
    }

    onExport(e){
        e.preventDefault();
        console.log(`export type ${this.state.type}`);
        this.props.exportProducts(this.state.show_all, this.buildSort(), this.state.region, this.state.type, this.state.search_term);
    }

    onChangePageSize(e) {
        let target = e.currentTarget;
        let val     = target.value;
        this.setState({...this.state, page_size: val});
    }

    render() {
        // build pagination ...
        let pages = [];
        for(let i = 0; i < this.props.page_count; i++)
            pages.push
            (
                <li key={i} className={ (i+1) == this.state.current_page ? "active" : "" }>
                    <a href="#" data-page={i+1} onClick={(e) => this.onChangePage(e)}>{i+1}</a>
                </li>
            );

        let filterName = '';
        if(this.state.sort_field == 'name'){
            filterName = <span className={ this.state.sort_direction == SortDirectionAsc ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down' } aria-hidden="true"></span>
        }

        let filterType = '';
        if(this.state.sort_field == 'type'){
            filterType = <span className={ this.state.sort_direction == SortDirectionAsc ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down' } aria-hidden="true"></span>
        }

        let filterExpiryDate = '';
        if(this.state.sort_field == 'expiry_date'){
            filterExpiryDate = <span className={ this.state.sort_direction == SortDirectionAsc ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down' } aria-hidden="true"></span>
        }

        return (
            <div>
                <h3>OpenStack Products By Region</h3>
                <div className="row" style={{ marginBottom: "25px"}}>
                    <div className="col-md-12">
                        <button className="btn btn-sm btn-default" onClick={(e) => this.onExport(e)}>Export</button>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-4">
                        <select id="filterProductType" className="form-control" onChange={(e) => this.onProductTypeFilterChange(e)}>
                            <option value="ALL">--ALL--</option>
                            <option value="DISTRIBUTION">Distributions</option>
                            <option value="APPLIANCE">Appliance</option>
                            <option value="REMOTECLOUD">Remote Cloud</option>
                            <option value="PUBLICCLOUD">Public Cloud</option>
                            <option value="PRIVATECLOUD">Private Cloud</option>
                        </select>
                    </div>
                    <div className="col-md-4">
                        <select id="filterRegion" className="form-control" onChange={(e) => this.onRegionFilterChange(e)}>
                            <option value="ALL">--ALL--</option>
                            {this.props.regions.map(
                                region =>
                                <option key={region.name} value={region.name}>{region.name}</option>
                            )}
                        </select>
                    </div>
                    <div className="col-md-4">
                        <input type="text" className="form-control" onChange={(e) => this.onFilterByCompanyName(e) } placeholder="Company Name" id="filterProductCompany"/>
                    </div>
                </div>
                <table className="table">
                    <thead>
                    <tr>
                        <th>
                            <a title="Order by Name" onClick={(e) => this.onChangeSorting(e, 'name')} href="#">
                            { filterName }&nbsp;Name
                            </a>
                        </th>
                        <th>
                            <a title="Order by Type" onClick={(e) => this.onChangeSorting(e, 'type')} href="#">
                            { filterType }&nbsp;Type
                            </a>
                        </th>
                        <th>Company</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Region</th>
                        <th>Contacts</th>
                        <th>Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    {
                        this.props.items.map
                        (
                            product =>

                            <tr key={product.id}>
                                <td>
                                    <a href={`sangria/ViewPoweredOpenStackProductDetail/${product.id}`} target="_blank">{product.name}</a>
                                </td>
                                <td>
                                    {product.type}
                                </td>
                                <td>
                                    {product.company}
                                </td>
                                <td>
                                    {product.city}
                                </td>
                                <td>
                                    {product.country}
                                </td>
                                <td>
                                    {product.region}
                                </td>
                                <td>
                                    {product.admins}
                                </td>
                                <td>
                                    {product.notes}
                                </td>
                            </tr>
                        )
                    }
                    </tbody>
                </table>

                <nav aria-label="Page navigation">
                    <select defaultValue={this.props.page_size} className="form-control page-size-control"  onChange={(e) => this.onChangePageSize(e)} name="pagination_page_size">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="0">--ALL--</option>
                    </select>
                    <ul className="pagination">
                        {pages}
                    </ul>
                </nav>
            </div>
        );
    }
}

export default connect (
    state => {
        return {
            items:      state.items,
            page_count: state.page_count,
            loading:    state.loading,
        }
    },
    dispatch => ({
        fetchPage (page = 1, page_size = 25, show_all = 1, order = '', region = 'ALL', type = 'ALL', search_term = '') {
            console.log('fetchPage');
            return dispatch(fetchAllProducts({page, page_size, show_all, order, region, type, search_term}));
        },
        exportProducts(show_all = 1, order = '', region = 'ALL', type = 'ALL', search_term = ''){
            return dispatch(exportAllProducts({show_all, order, region, type, search_term}));
        }
    })
)(SangriaOpenStackProductsByRegionApp);
