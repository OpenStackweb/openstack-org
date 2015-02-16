/**
 * @jsx React.DOM
 */
var React = require('react/addons');
var BS = require('react-bootstrap');

module.exports = React.createClass({

	render: function () {
		var cx = React.addons.classSet;
		var item = this.props.presentation;
		var classes = cx({
			'presentation-list-item': true,
			'active': this.props.active,
			'hasVoted': !!item.user_vote			
		});

		return (
		<div className={classes}>
			<h4>{item.title}</h4>
		</div>
		);
	}
});