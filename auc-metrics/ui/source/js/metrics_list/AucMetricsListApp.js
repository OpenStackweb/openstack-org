/**
 * Copyright 2018 OpenStack Foundation
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
import { connect } from 'react-redux';
import { fetchPage, exportAll, } from './actions';
import BaseReport from "~core-components/base_report";

class DatePicker extends React.Component {
    componentDidMount() {
        let _this = this;
        let {onChange} = this.props;
        $(this.textInput).datetimepicker({
            format: 'Y-m-d H:i:00',
            step: 1,
            formatDate: 'Y-m-d',
            formatTime: 'H:i:00',
            defaultTime: '23:59:00'
        });

        $(this.textInput).change(function(){
            onChange({currentTarget:_this.textInput});
        })
    }

    componentWillUnmount() {
        $(this.textInput).datetimepicker('destroy');
    }

    render() {
        const {className, defaultValue, placeHolder} = this.props;
        return <input ref={(input) => { this.textInput = input; }}
                      placeholder={placeHolder}
                      defaultValue={defaultValue}
                      className={className}
                      type="text"  />
    }
}

class AucMetricsListApp extends BaseReport {

    constructor(props) {
        super(props);
        this.state = {
            ...this.state,
            from_date: null,
            to_date: null,
         };
    }

    componentDidMount() {
        let _this = this;
        if (!this.props.items.length) {
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                order: this.buildSort(),
                'filter[]':this.buildFilter(),
            });
        }
    }

    onExport(e){
        e.preventDefault();
        this.props.exportAll({
            search_term: this.state.search_term,
            type: this.state.type,
            order: this.buildSort(),
            'filter[]':this.buildFilter(),
        });
    }

    componentDidUpdate(prevProps, prevState) {
        if
        (
            prevState.sort_direction != this.state.sort_direction ||
            prevState.sort_field != this.state.sort_field ||
            prevState.current_page != this.state.current_page ||
            prevState.type != this.state.type ||
            prevState.search_term != this.state.search_term ||
            prevState.page_size != this.state.page_size ||
            this.shouldUpdateByDates(prevState)
        )
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort(),
                'filter[]':this.buildFilter(),
            });
    }

    shouldUpdateByDates(prevState){
        if
        (
            ( prevState.from_date != this.state.from_date ||
              prevState.to_date != this.state.to_date )
            && this.state.to_date && this.state.from_date
        )
        {
            let from = new Date(this.state.from_date);
            let to   = new Date(this.state.to_date);
            return (from < to);
        }
        return false;
    }

    buildFilter(){
        if(this.state.from_date && this.state.to_date){
            let from = new Date(this.state.from_date) ;
            let to   = new Date(this.state.to_date);
            if(from < to)
                return [
                  `from_date>=${this.state.from_date}`,
                  `to_date<=${this.state.to_date}`,
                ];
        }
        return null;
    }

    onChangeFromDate(e){
        let target  = e.currentTarget;
        let val     = target.value;
        this.setState({...this.state, from_date: val});
    }

    onChangeToDate(e){
        let target  = e.currentTarget;
        let val     = target.value;
        let filter  = [];
        this.setState({...this.state, to_date: val});
    }

    // to override if needed
    renderColumn(item, col){
        switch(col.name){
            default:
                return item[col.name];
        }
    }

    // to override if needed
    renderCustomPrimaryFilter() {
        return (
            <div className="dates-container col-md-6">
                <div className="row">
                    <div className="col-md-6">
                        <DatePicker placeHolder="Enter From Date"
                                    className="date-selector form-control"
                                    onChange={(e) => this.onChangeFromDate(e)}
                        />
                    </div>
                    <div className="col-md-6">
                        <DatePicker placeHolder="Enter To Date"
                                    className="date-selector form-control"
                                    onChange={(e) => this.onChangeToDate(e)}
                        />
                    </div>
                </div>
            </div>
        );
    }
}

function mapStateToProps(state) {
    return {
        items:      state.items,
        page_count: state.page_count,
        loading:    state.loading
    }
}

export default connect(mapStateToProps, {
    fetchPage,
    exportAll,
})(AucMetricsListApp)
