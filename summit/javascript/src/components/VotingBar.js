/**
 * @jsx React.DOM
 */
var React = require('react');
var ToggleButtons = require('./ToggleButtons');

module.exports = React.createClass({

	keyUpListener: function (e) {
		var ref;
		var code = e.keyCode;

		if(code === 48 || code === 96) ref = "0";
		if(code === 49 || code === 97) ref = "-1";
		if(code === 50 || code === 98) ref = "1";
		if(code === 51 || code === 99) ref = "2";
		if(code === 52 || code === 100) ref ="3";
	
		if(ref) {			
			this.props.onChange(ref);
		}

	},


	componentDidMount: function () {
		document.addEventListener("keyup", this.keyUpListener);
	},


	componentDidUnmount: function () {
		document.removeEventListener("keyup", this.keyUpListener);
	},


	render: function() {
		return this.transferPropsTo(
			<ToggleButtons />
		);
	}
});
