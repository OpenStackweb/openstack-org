import React from 'react';
import { connect } from 'react-redux';
import { fetchAllProducts, updateProductField, exportAllProducts, navigateToProductDetails } from './actions';
import { AjaxLoader } from '../../../../../ui-core/ui/source/js/components/ajaxloader';

const SortDirectionAsc  = 'ASC';
const SortDirectionDesc = 'DESC';

class DatePicker extends React.Component {
    componentDidMount() {
        let _this = this;
        $(this.textInput).datetimepicker({
            format: 'Y-m-d H:i:00',
            step: 1,
            formatDate: 'Y-m-d',
            formatTime: 'H:i:00',
            defaultTime: '23:59:00',
            onSelectTime:(dp,input) => {
                console.log(`onChangeDateTime ${input.val()}`);
                let event = new Event('input', { bubbles: true });
                _this.textInput.dispatchEvent(event);
            }
        });
    }

    componentWillUnmount() {
        $(this.textInput).datetimepicker('destroy');
    }

    render() {
        const props = this.props;
        return <input ref={(input) => { this.textInput = input; }} placeholder="Enter a Date" type="text" {...props} />
    }
}

class OpenStackProgramVersionSelector extends React.Component{

    render() {
        let options                             = [];
        let {onChange, className, defaultValue} = this.props;

        for (let program_version of this.props.items) {
            options.push(<option key={program_version.id} value={program_version.id}>{program_version.name}</option>)
        }

        return (
            <select defaultValue={defaultValue} className={className} onChange={onChange}>
                <option value="">--SELECT ONE --</option>
                {options}
            </select>
        );
    }
}

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

class SangriaOpenStackPoweredProductsApp extends React.Component
{
    constructor(props) {
        super(props);
        this.state = {
            sort_direction  : SortDirectionAsc,
            sort_field      : 'name',
            current_page: 1,
            page_size: 25,
            show_all : 1,
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
            prevState.type != this.state.type ||
            prevState.search_term != this.state.search_term ||
            prevState.show_all != this.state.show_all ||
            prevState.page_size != this.state.page_size
        )
            this.props.fetchPage(this.state.current_page, this.state.page_size, this.state.show_all, this.buildSort(), this.state.type, this.state.search_term);
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

    onChangeRequiredForCompute(e, product){
        let target = e.currentTarget;
        let val    = target.checked;
        console.log(`onChangeRequiredForCompute value ${val} productId ${product.id}`);
        this.props.updateProduct(product, 'required_for_compute', val);
    }

    onChangeRequiredForStorage(e, product){
        let target = e.currentTarget;
        let val    = target.checked;
        console.log(`onChangeRequiredForStorage value ${val} productId ${product.id}`);
        this.props.updateProduct(product, 'required_for_storage', val);
    }

    onChangeFederatedIdentity(e, product){
        let target = e.currentTarget;
        let val    = target.checked;
        console.log(`onChangeFederatedIdentity value ${val} productId ${product.id}`);
        this.props.updateProduct(product, 'federated_identity', val);
    }

    onChangeProgramVersion(e, product){
        let target             = e.currentTarget;
        let val                = target.value;
        console.log(`onChangeProgramVersion value ${val} productId ${product.id}`);
        this.props.updateProduct(product, 'program_version_id', val);
    }

    onChangeExpiryDate(e, product){
        let target     = e.currentTarget;
        let val        = target.value;
        console.log(`onChangeExpiryDate value ${val} productId ${product.id}`);
        this.props.updateProduct(product, 'expiry_date', val);
    }

    onShowStatusFilter(e, status){
        e.preventDefault();
        this.setState({...this.state, show_all: status == 'ALL' ? 1: 0, current_page: 1 });
    }

    onExport(e){
        e.preventDefault();
        console.log(`export type ${this.state.type}`);
        this.props.exportProducts(this.state.show_all, this.buildSort(), this.state.type, this.state.search_term);
    }

    navigate2ProductDetails(e, product){
        e.preventDefault();
        this.props.navigate2ProductDetails(product);
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
                <h3>OpenStack Powered Products</h3>
                <div className="row" style={{ marginBottom: "25px"}}>
                    <div className="col-md-12">
                        <span>Show: </span>
                        <FilterLink onClick={(e) => this.onShowStatusFilter(e, 'ALL')} filter='ALL' currentFilter={this.state.show_all == 1 ? 'ALL' : 'EXPIRED' }>
                            All
                        </FilterLink>
                        {' | '}
                        <FilterLink onClick={(e) => this.onShowStatusFilter(e, 'EXPIRED')} filter='EXPIRED' currentFilter={this.state.show_all == 1 ? 'ALL' : 'EXPIRED' }>
                            Expired
                        </FilterLink>
                        {' | '}
                        <button onClick={(e) => this.onExport(e)}>Export</button>
                    </div>
                </div>
                <div className="row">
                    <div className="col-md-6">
                        <select id="filterProductType" className="form-control" onChange={(e) => this.onProductTypeFilterChange(e)}>
                            <option value="ALL">--ALL--</option>
                            <option value="DISTRIBUTION">Distributions</option>
                            <option value="APPLIANCE">Appliance</option>
                            <option value="REMOTECLOUD">Remote Cloud</option>
                            <option value="PUBLICCLOUD">Public Cloud</option>
                            <option value="PRIVATECLOUD">Private Cloud</option>
                        </select>
                    </div>
                    <div className="col-md-6">
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
                        <th>Required for Compute</th>
                        <th>Required for Storage</th>
                        <th>Federated Identity</th>
                        <th>Program Version Compatibility</th>
                        <th>
                            <a title="Order by Expiry Date" onClick={(e) => this.onChangeSorting(e, 'expiry_date')} href="#">
                            { filterExpiryDate }&nbsp;Expiry Date (CDT)
                            </a>
                        </th>
                        <th>Last Edited By</th>
                        <th>&nbsp;</th>
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
                                    <input type="checkbox" defaultChecked={product.required_for_compute} onChange={(e) => this.onChangeRequiredForCompute(e, product)}/>
                                </td>
                                <td>
                                    <input type="checkbox" defaultChecked={product.required_for_storage} onChange={(e) => this.onChangeRequiredForStorage(e, product)}/>
                                </td>
                                <td>
                                    <input type="checkbox" defaultChecked={product.federated_identity} onChange={(e) => this.onChangeFederatedIdentity(e, product)}/>
                                </td>
                                <td>
                                    <OpenStackProgramVersionSelector className="form-control" items={this.props.program_versions} defaultValue={product.program_version_id} onChange={(e) => this.onChangeProgramVersion(e, product)} />
                                </td>
                                <td>
                                    <DatePicker className="expiry-date-selector form-control" defaultValue={product.expiry_date} onChange={(e) => this.onChangeExpiryDate(e, product)} />
                                </td>
                                <td>
                                    {product.edited_by}
                                </td>
                                <td>
                                    <button onClick={(e) => this.navigate2ProductDetails(e, product)} >Detail</button>
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
        fetchPage (page = 1, page_size = 25, show_all = 1, order = '', type = 'ALL', search_term = '') {
            console.log('fetchPage');
            return dispatch(fetchAllProducts({page, page_size, show_all, order, type, search_term}));
        },
        updateProduct(product, field, value){
            let payload    = {};
            payload[field] = value;
            return dispatch(updateProductField({ product_id: product.id}, payload));
        },
        exportProducts(show_all = 1, order = '', type = 'ALL', search_term = ''){
            return dispatch(exportAllProducts({show_all, order, type, search_term}));
        },
        navigate2ProductDetails(product){
            return dispatch(navigateToProductDetails(product.id));
        }
    })
)(SangriaOpenStackPoweredProductsApp);
