import React, {Component} from 'react'
import ReactDOM from 'react-dom'
import thunk from 'redux-thunk'
import { createStore, applyMiddleware } from 'redux'

const createStoreWithMiddleware = applyMiddleware(thunk)(createStore);
require("awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

const element = document.getElementById('os-schedule-global-search');

const props = {
    search_url: element.getAttribute('data-search-url'),
    search_value: element.getAttribute('data-search-value'),
    schedule_url: element.getAttribute('data-schedule-url'),
}

class GlobalSearch extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            search: props.search_value,
        };
    }

    handleSearchChange(event) {
        this.setState({search: event.target.value});
    }

    handleClearClick(event) {
        event.preventDefault();
        if (this.state.search == '') return;
        this.setState({search: ''});
        if (this.state.schedule_url == '') return;
        window.location = this.props.schedule_url;
    }

    render() {
        return (
            <div className="row global-search-container">
                <form id="form-schedule-global-search" className="form-inline all-events-search-form" action={ this.props.search_url }>
                    <div className="col-xs-12 col-sm-4 col-sm-offset-8 global-search-div">
                        <div className="input-group">
                            <input value={this.state.search} id="t" name="t"
                            className="form-control input-global-search"
                            placeholder="Search by Track/Event/Title/Company"
                            onChange={e => this.handleSearchChange(e)}
                            />
                            <span className="input-group-btn" style={{width: '5%'}}>
                                <button className="btn btn-default btn-global-search" type="submit">
                                    <i className="fa fa-search"></i>
                                </button>
                                <button className="btn btn-default btn-global-search-clear" onClick={e => this.handleClearClick(e)}>
                                    <i className="fa fa-times"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        );
    }
}

ReactDOM.render(
    <GlobalSearch {...props} />,
    document.querySelector('.os-schedule-global-search')
);