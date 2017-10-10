import React from 'react';
import { connect } from 'react-redux';
import { fetchAll, updateItem } from './actions';
import Message from "~core-components/message";

const SortDirectionAsc  = 'ASC';
const SortDirectionDesc = 'DESC';

class PublicCloudPassportsApp extends React.Component
{
    constructor(props) {
        super(props);
        this.state = {
            items: [],
            sort_direction  : SortDirectionAsc,
            sort_field      : 'name',
            current_page: 1,
            page_size: 25,
            search_term: '',
            loading: false
        }
    }

    componentDidMount(){
        if(!this.state.items.length) {
            this.props.fetchPage(this.state.current_page, this.state.page_size, this.buildSort());
        }
    }

    componentDidUpdate(prevProps, prevState){
        if
        (
            prevState.sort_direction != this.state.sort_direction ||
            prevState.sort_field != this.state.sort_field ||
            prevState.current_page != this.state.current_page ||
            prevState.search_term != this.state.search_term ||
            prevState.page_size != this.state.page_size
        )
            this.props.fetchPage(this.state.current_page, this.state.page_size, this.buildSort(), this.state.search_term);
    }

    componentWillReceiveProps(nextProps) {
        this.setState({...this.state, items: nextProps.items});
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

    onFilterByCompanyName(e){
        let target = e.currentTarget;
        let val    = target.value;
        console.log('onFilterByCompanyName value '+ val);
        this.setState({...this.state, search_term: val, current_page: 1});
    }

    onChangeField(e, item){
        let target = e.currentTarget;
        let val    = (target.type == 'checkbox') ? (target.checked ? 1 : 0) : target.value;
        let field  = target.name;
        let state_item = this.state.items.find( i => i.id == item.id );

        if (field == 'learn_more') {
            if ( val.length > 4 && val.indexOf("http") !== 0) {
                state_item.error = 1;
            } else {
                state_item.error = 0;
            }
        }

        state_item[field] = val;
        let state_items = this.state.items.map((i) => i.id == item.id ? state_item : i);

        this.setState({...this.state, items: state_items});
    }

    onChangePageSize(e) {
        let target = e.currentTarget;
        let val     = target.value;
        this.setState({...this.state, page_size: val});
    }

    onClickSave(e, item) {
        if (item.error != 1)
            this.props.saveItem(item);
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

        return (
            <div>
                <Message />
                <h3>Public Cloud Passports</h3>
                <div className="row">
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
                        <th>Company</th>
                        <th>Passport Program</th>
                        <th>Learn More</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    {
                        this.state.items.map
                        (
                            item =>

                            <tr key={item.id}>
                                <td>
                                    <a href={`/marketplace/public-clouds/${item.slug}`} target="_blank">{item.name}</a>
                                </td>
                                <td>
                                    {item.company}
                                </td>
                                <td>
                                    <input type="checkbox" name="is_passport" defaultChecked={item.is_passport} onChange={(e) => this.onChangeField(e, item)}/>
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="learn_more"
                                        disabled={!item.is_passport}
                                        className={'form-control ' + (item.error == 1 ? 'input_error' : '')}
                                        defaultValue={item.learn_more}
                                        onChange={(e) => this.onChangeField(e, item)}
                                    />
                                </td>
                                <td>
                                    <button className="btn btn-sm btn-primary" disabled={item.error == 1} onClick={(e) => this.onClickSave(e, item)} >Save</button>
                                </td>
                            </tr>
                        )
                    }
                    </tbody>
                </table>

                <nav aria-label="Page navigation">
                    <select defaultValue={this.state.page_size} className="form-control page-size-control"  onChange={(e) => this.onChangePageSize(e)} name="pagination_page_size">
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
            loading:    state.loading
        }
    },
    dispatch => ({
        fetchPage (page = 1, page_size = 25, order = '', search_term = '') {
            console.log('fetchPage');
            return dispatch(fetchAll({page, page_size, order, search_term}));
        },
        saveItem(item){
            return dispatch(updateItem({ item }));
        }
    })
)(PublicCloudPassportsApp);
