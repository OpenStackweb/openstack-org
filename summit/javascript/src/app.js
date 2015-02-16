/**
 * @jsx React.DOM
 */

// React components
var React = require('react');
var BS = require('react-bootstrap');
var Navigation = require('./components/Navigation');

module.exports = React.createClass({

	displayName: "Presentation App",

	getInitialState: function () {
		return {
			errorMsg: false
		};
	},

	render: function () {
		return (
			<div>
				<Navigation />
				{this.state.errorMsg && 
					<BS.Alert bsStyle="warning" onDismiss={this.handleDismiss}>
					      {this.state.errorMsg}
					</BS.Alert>
				}
				<this.props.activeRouteHandler />
			</div>
		);
	}
});