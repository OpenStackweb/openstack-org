/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

import React from 'react';
import Message from "~core-components/message";
import './styles.less';

const SortDirectionAsc  = 'ASC';
const SortDirectionDesc = 'DESC';

const FilterColumn = ({col, onChangeSorting, currentSortField, currentSortDirection}) => {
    let filterDecorator = '';
    if(currentSortField == col.name){
        filterDecorator = <span className={ currentSortDirection == SortDirectionAsc ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down' } aria-hidden="true"></span>
    }
    return (
        <a title={col.title} onClick={(e) => onChangeSorting(e, col.name)} href="#">
            { filterDecorator }&nbsp;{col.label}
        </a>
    );
}

class BaseReport
    extends React.Component {

    constructor(props) {
        super(props);
        let { reportConfig } = props;
        this.state = {
            sort_direction  : SortDirectionAsc,
            sort_field      : reportConfig.defaultSortField,
            current_page    : 1,
            page_size       : reportConfig.initialPageSize,
            type            : 'ALL',
            search_term     : '',
        };
        this.onChangeSorting  = this.onChangeSorting.bind(this);
        this.onFilterFreeText = this.onFilterFreeText.bind(this);
        this.onExport         = this.onExport.bind(this);
        this.onChangePageSize = this.onChangePageSize.bind(this);
    }

    componentDidMount(){
        if(!this.props.items.length) {
            this.props.fetchPage({
                page : this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort()
            });
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
            prevState.page_size != this.state.page_size
        )
            this.props.fetchPage({
                page : this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort()
            });
    }

    onFilterFreeText(e){
        let target = e.currentTarget;
        let val    = target.value;
        this.setState({...this.state, search_term: val, current_page: 1});
    }

    onChangeSorting(e, property){
        e.preventDefault();
        console.log(`onChangeSorting for property ${property}`);
        this.setState({...this.state, sort_field: property, sort_direction: this.state.sort_direction == SortDirectionAsc ? SortDirectionDesc : SortDirectionAsc });
        return false;
    }

    buildSort(){
        let dir = this.state.sort_direction == SortDirectionAsc ? '+':'-';
        return `${this.state.sort_field}${dir}`;
    }

    calculateSecondaryFilterColQty(){
        let {reportConfig} = this.props;
        return reportConfig.hasCustomSecondaryFilter?'4':'6';
    }

    onChangePage(e){
        e.preventDefault();
        let target         = e.currentTarget;
        let current_page   = target.attributes.getNamedItem('data-page').value;
        this.setState({...this.state, current_page});
    }

    onTypeFilterChange(e){
        let target = e.currentTarget;
        let val    = target.value;
        this.setState({...this.state, type: val, current_page: 1});
    }

    onExport(e){
        e.preventDefault();
        this.props.exportAll({
            search_term: this.state.search_term,
            type: this.state.type,
            order: this.buildSort()
        });
    }

    onChangePageSize(e) {
        let target = e.currentTarget;
        let val     = target.value;
        this.setState({...this.state, page_size: val, current_page: 1});
    }

    renderItem(item){
        let {reportConfig} = this.props;
        let tr = [];
        reportConfig.columns.map((col, index) => {
            tr.push(
                <td key={index}>{this.renderColumn(item, col)}</td>
            )
        });
        return tr;
    }

    // to override if needed
    renderColumn(item, col){
        return item[col.name]
    }

    // to override if needed
    renderCustomPrimaryFilter(){
        return null;
    }

    // to override if needed
    renderCustomSecondaryFilter(){
        return null;
    }

    render(){
        // build pagination ...
        let {reportConfig} = this.props;
        let pages = [];
        for(let i = 0; i < this.props.page_count; i++)
            pages.push
            (
                <li key={i} className={ (i+1) == this.state.current_page ? "active" : "" }>
                    <a href="#" data-page={i+1} onClick={(e) => this.onChangePage(e)}>{i+1}</a>
                </li>
            );

        return (
            <div>
                <Message />
                <h3>{reportConfig.title}</h3>
                <div className="row" style={{ marginBottom: "25px"}}>
                    <div className="col-md-12">
                        {this.renderCustomPrimaryFilter()}
                        <button className="btn btn-sm btn-default" onClick={this.onExport}>Export</button>
                    </div>
                </div>
                <div className="row">
                    <div className={"col-md-"+this.calculateSecondaryFilterColQty()}>
                        {reportConfig.filterTypes.length > 0 &&
                            <select id="filterType" className="form-control" onChange={(e) => this.onTypeFilterChange(e)}>
                                <option value="ALL">--ALL--</option>
                                {
                                    reportConfig.filterTypes.map((filterType, index) => (
                                        <option key={index} value={filterType.value}>{filterType.label}</option>
                                    ))
                                }
                            </select>
                        }
                    </div>
                    {
                        reportConfig.hasCustomSecondaryFilter &&
                        <div className={"col-md-"+this.calculateSecondaryFilterColQty()}>
                            {this.renderCustomSecondaryFilter()}
                        </div>
                    }
                    <div className={"col-md-"+this.calculateSecondaryFilterColQty()}>
                        <input type="text" className="form-control"
                               onChange={this.onFilterFreeText}
                               placeholder={reportConfig.freeTextSearchPlaceHolder}
                               id="filterFreeText"/>
                    </div>
                </div>
                <table className="table">
                    <thead>
                    <tr>
                        {
                            reportConfig.columns.map((col, index) => (
                                <th key={index}>{ col.shouldSort ? <FilterColumn col={col} onChangeSorting={this.onChangeSorting} currentSortField={this.state.sort_field} currentSortDirection={this.state.sort_direction} />: col.label}</th>
                            ))
                        }
                    </tr>
                    </thead>
                    <tbody>
                    {
                        this.props.items.map
                        (
                            (item, index) => <tr key={index}>
                                {this.renderItem(item)}
                            </tr>
                        )
                    }
                    </tbody>
                </table>
                <nav aria-label="Page navigation">
                    <select defaultValue={this.props.pageSize}
                            className="form-control page-size-control"
                            onChange={this.onChangePageSize}
                            name="pagination_page_size">
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

BaseReport.propTypes = {
    reportConfig: React.PropTypes.object.isRequired,
}

export default BaseReport;