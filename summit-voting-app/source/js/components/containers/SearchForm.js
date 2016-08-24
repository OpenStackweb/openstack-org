import React from 'react';
import { connect } from 'react-redux';
import { setSearchTerm } from '../../action-creators';
import { pushState } from 'redux-router';
import url from '../../utils/url';

class SearchForm extends React.Component {

	constructor (props) {
		super(props);
		this.state = {
			searchQuery: props.initialQuery
		};
		this.handleSearchTyped = this.handleSearchTyped.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}


	handleSearchTyped (e) {
		this.setState({
			searchQuery: e.target.value
		})	
	}


	handleSubmit (e) {		
		e.preventDefault();
		this.props.setSearchTerm(this.state.searchQuery);
	}


	render () {
		const { searchQuery } = this.state;
		return (
			<form onSubmit={this.handleSubmit}>
				<input 					
					onChange={this.handleSearchTyped}
					className="text voting-search-input"
					value={searchQuery}
					placeholder="Search"
					type="text"
					onBlur={this.handleSubmit}
					name="Search" />
			</form>
		);


	}
}

export default connect (
	state => ({		
		initialQuery: state.presentations.search || ''
	}),
	{
		setSearchTerm
	}
)(SearchForm);