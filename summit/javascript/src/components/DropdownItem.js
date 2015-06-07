var React = require('react');

var DropdownItem = React.createClass({displayName:'DropdownItem',

	render: function() {		
		return (
          <li onClick={this.props.onClick}>
            <a href='#'>
              {this.props.children}
            </a>
          </li>		
		);
	}

});

module.exports = DropdownItem;