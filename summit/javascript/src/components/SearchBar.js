var React = require('react');

var SearchBar = React.createClass({

	DELAY: 300,


	_timeout: null,


	getDefaultProps: function() {
		return {
			initialText: '',
			onUpdate: function () {}
		};
	},


	getInitialState: function() {
		return {
			searchText: this.props.initialText 
		};
	},


	_handleChange: function (e) {
		this.setState({
			searchText: e.target.value
		});

		if(this._timeout) window.clearTimeout(this._timeout);

		this._timeout = window.setTimeout(function() {
			this.props.onUpdate(this.refs.search.getDOMNode().value);
		}.bind(this), this.DELAY);
	},


	render: function() {
		return (
			<input {...this.props} value={this.state.searchText} type="text" ref="search" onChange={this._handleChange} />
		);
	}

});
module.exports = SearchBar;