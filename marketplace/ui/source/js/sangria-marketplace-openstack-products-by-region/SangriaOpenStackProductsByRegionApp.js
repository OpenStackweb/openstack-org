import React from 'react';
import { connect } from 'react-redux';
import { exportAll, fetchPage } from './actions';
import BaseReport from "~core-components/base_report";

class SangriaOpenStackProductsByRegionApp extends BaseReport {

    constructor(props) {
        super(props);
        this.state = {
            ...this.state,
            region: 'ALL',
        };
        this.onCustomSecondaryFilterChange = this.onCustomSecondaryFilterChange.bind(this);
    }

    componentDidMount() {
        if (!this.props.items.length) {
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort(),
                region: this.state.region,
            });
        }
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
            prevState.region != this.state.region
        )
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort(),
                region: this.state.region,
            });
    }

    // to override if needed
    renderCustomSecondaryFilter(){
        return (
            <select id="filterRegion" className="form-control" onChange={this.onCustomSecondaryFilterChange}>
                <option value="ALL">--ALL--</option>
                {this.props.regions.map(
                    region =>
                        <option key={region.name} value={region.name}>{region.name}</option>
                )}
            </select>
        );
    }

    onCustomSecondaryFilterChange(e){
        let target = e.currentTarget;
        let val    = target.value;
        this.setState({...this.state, region: val, current_page: 1});
    }

    onExport(e) {
        e.preventDefault();
        this.props.exportAll({
            search_term: this.state.search_term,
            type: this.state.type,
            order: this.buildSort(),
            region: this.state.region
        });
    }

    // to override if needed
    renderColumn(item, col){
        switch(col.name){
            case 'name':
                return (<a href={`sangria/ViewPoweredOpenStackProductDetail/${item.id}`} target="_blank">{item.name}</a>);
            default:
                return item[col.name];
        }
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
})(SangriaOpenStackProductsByRegionApp)