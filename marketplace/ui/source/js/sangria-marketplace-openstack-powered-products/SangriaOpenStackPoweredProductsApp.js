import React from 'react';
import { connect } from 'react-redux';
import { fetchPage, updateProductField, exportAll, navigateToProductDetails } from './actions';
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
        const {className, defaultValue} = this.props;
        return <input ref={(input) => { this.textInput = input; }}
                      placeholder="Enter a Date"
                      value={defaultValue}
                      className={className}
                      type="text"  />
    }
}

const OpenStackProgramVersionSelector = ({onChange, className, defaultValue, items}) => {
    let options = [];
    for (let program_version of items) {
        options.push(<option key={program_version.id} value={program_version.id}>{program_version.name}</option>)
    }

    return (
        <select defaultValue={defaultValue} className={className} onChange={onChange}>
            <option value="">--SELECT ONE --</option>
            {options}
        </select>
    );
}

const FilterLink = ({filter, currentStatus, onClick, children} ) => {
    let active = (currentStatus) ? 'active' : '';
    return (
        <label className={"btn btn-primary btn-sm " + active} onClick={onClick} data-filter={filter}>
            <input type="checkbox" defaultChecked={currentStatus} autoComplete="off"/> {children}
        </label>
    );
}

class SangriaOpenStackPoweredProductsApp extends BaseReport {

    constructor(props) {
        super(props);
        this.state = {
            ...this.state,
            show_expired: 0,
            show_powered: 0
        };
        this.onCustomPrimaryFilterChange = this.onCustomPrimaryFilterChange.bind(this);
    }

    componentDidMount() {
        if (!this.props.items.length) {
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort(),
                show_expired: this.state.show_expired,
                show_powered: this.state.show_powered
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
            prevState.show_expired != this.state.show_expired ||
            prevState.show_powered != this.state.show_powered
        )
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort(),
                show_expired: this.state.show_expired,
                show_powered: this.state.show_powered
            });
    }

    onExport(e) {
        e.preventDefault();
        this.props.exportAll({
            search_term: this.state.search_term,
            type: this.state.type,
            order: this.buildSort(),
            show_expired: this.state.show_expired,
            show_powered: this.state.show_powered
        });
    }

    onChangeCheckBoxField(e, product, fieldName) {
        let target = e.currentTarget;
        let val = target.checked;
        let payload = {};
        payload[fieldName] = val;
        this.props.updateProductField({product_id: product.id}, payload);
    }

    onChangeValueField(e, product, fieldName) {
        let target  = e.currentTarget;
        let val     = target.value;
        let payload = {};
        console.log(`onChangeValueField value ${val}`);
        payload[fieldName] = val;
        this.props.updateProductField({product_id: product.id}, payload);
    }

    onCustomPrimaryFilterChange(e) {
        e.preventDefault();
        let filter = e.target.dataset.filter;
        let status = e.target.firstElementChild.checked ? 1 : 0;
        switch (filter) {
            case 'EXPIRED':
                this.setState({...this.state, show_expired: status, current_page: 1});
                break;
            case 'POWERED':
                this.setState({...this.state, show_powered: status, current_page: 1});
                break;
        }
    }

    // to override if needed
    renderColumn(item, col){
        switch(col.name){
            case 'name':
                return (<a href={`sangria/ViewPoweredOpenStackProductDetail/${item.id}`} target="_blank">{item.name}</a>);
            case 'federated_identity':
            case 'required_for_storage':
            case 'required_for_compute':
                return (<input type="checkbox" defaultChecked={item[col.name]} onChange={(e) => this.onChangeCheckBoxField(e, item, col.name)}/>);
            case 'expiry_date':
                return (<DatePicker className="expiry-date-selector form-control"
                                    defaultValue={item.expiry_date}
                                    onChange={(e) => this.onChangeValueField(e, item, col.name)} />);
            case 'program_version_id':
                return (<OpenStackProgramVersionSelector className="form-control"
                                                         items={this.props.programVersions}
                                                         defaultValue={item.program_version_id}
                                                         onChange={(e) => this.onChangeValueField(e, item, col.name)}/>);
            case 'action_buttons':
                return (
                    <button className="btn btn-sm btn-default" onClick={(e) => this.navigate2ProductDetails(e, item)}>
                        Detail
                    </button>
                );
            default:
                return item[col.name];
        }
    }

    // to override if needed
    renderCustomPrimaryFilter() {
        return (
            <div className="btn-group" data-toggle="buttons" style={{marginRight: "10px"}}>
                <FilterLink onClick={this.onCustomPrimaryFilterChange} filter='EXPIRED'
                            currentStatus={this.state.show_expired}>
                    Expired
                </FilterLink>
                <FilterLink onClick={this.onCustomPrimaryFilterChange} filter='POWERED'
                            currentStatus={this.state.show_powered}>
                    Powered
                </FilterLink>
            </div>
        );
    }

    navigate2ProductDetails(e, product) {
        e.preventDefault();
        this.props.navigateToProductDetails(product.id);
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
    updateProductField,
    navigateToProductDetails
})(SangriaOpenStackPoweredProductsApp)