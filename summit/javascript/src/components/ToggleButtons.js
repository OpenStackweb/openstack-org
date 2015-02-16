/**
 * @jsx React.DOM
 */
var React = require('react');
var BS = require('react-bootstrap');

module.exports = React.createClass({

	render: function() {
		return (
			<BS.ButtonGroup>
			{this.props.options.map(function(item) {
				return (
					<BS.Button
						ref={"item-" + item.value}
						key={item.value}
						onClick={this.props.onChange.bind(null, item.value)} 
						active={this.props.value === item.value}>
							{item.label}
					</BS.Button>
				);
			}.bind(this))}
			</BS.ButtonGroup>
		);
	}
});
