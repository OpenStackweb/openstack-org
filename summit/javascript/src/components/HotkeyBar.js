var React = require('react/addons');

var HotkeyBar = React.createClass({


	_handleSelection: function (val) {
		this.setState({
			selection: val
		});
		this.props.onChange(val);
	},


	getInitialState: function() {
		return {
			selection: this.props.selection
		};
	},


	render: function() {
		var items = React.Children.map(this.props.children, function (child, index) {
	    	return (React.addons.cloneWithProps(child, {
	    		onChange: this._handleSelection.bind(null, child.props.val),
	    		selected: (this.state.selection == child.props.val)
	    	}));            	
	    }.bind(this));	    
		return (
            <ul className="voting-rate-wrapper">
            	{items}
            </ul>
		);
	}

});

module.exports = HotkeyBar;