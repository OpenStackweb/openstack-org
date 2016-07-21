import React from 'react';

class AutoCompleteResult extends React.Component {

	constructor(props) {
		super(props);
		this.handleClick = this.handleClick.bind(this);
	}

	handleClick(e) {
		e.preventDefault();		
		this.props.onSelect && this.props.onSelect(this.props.eventKey);
	}

	render() {
		return (
			<li><a onClick={this.handleClick}>{this.props.children}</a></li>
		);
	}
}

export default AutoCompleteResult;