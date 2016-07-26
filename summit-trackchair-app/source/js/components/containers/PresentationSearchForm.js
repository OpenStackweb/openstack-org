import React from 'react'
import {connect} from 'react-redux';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

class PresentationSearchForm extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			value: this.props.initialValue
		};

		this.updateValue = this.updateValue.bind(this);
		this.doSearch = this.doSearch.bind(this);
	}

	updateValue(e) {
		this.setState({
			value: e.target.value
		});
	}

	doSearch(e) {
		e.preventDefault();
		browserHistory.push(
			URL.create('/browse', {search: this.state.value})
		);
	}

	render () {
		return (
			<form onSubmit={this.doSearch}>
			<div className="input-group presentation-search">
	
			        <input value={this.state.value} onChange={this.updateValue} type="text" placeholder="Search presentations " className="input form-control" />
			        <span className="input-group-btn">
			                <button type="button" className="btn btn btn-primary"> <i className="fa fa-search"></i> Search</button>
			        </span>		    
		    </div>
		    </form>
	    );
	}
}


export default connect(
	state => ({
		initialValue: state.routing.locationBeforeTransitions.query.search,
	})
)(PresentationSearchForm);