var React = require('react/addons');


function isNodeInRoot(node, root) {
  while (node) {
    if (node === root) {
      return true;
    }
    node = node.parentNode;
  }

  return false;
}


var DropdownSelector = React.createClass({displayName: 'DropdownSelector',

	getInitialState: function() {
		return {
			selection: null,
			open: false
		};
	},

	componentDidMount: function() {
		document.addEventListener('click', this._bodyClickHandler);
	},


	componentWillUnmount: function() {
		document.removeEventListener('click', this._bodyClickHandler);
	},

	componentWillReceiveProps: function(nextProps) {
		if(!this.state.selection) {
			this.setState({
				selection: nextProps.defaultSelection
			});
		}
	},

	_bodyClickHandler: function(e) {
		if(isNodeInRoot(e.target, this.getDOMNode())) return;

		this.setState({
			open: false
		});
	},


	_handleSelection: function (key, e) {
		e.preventDefault();
		this.setState({
			selection: key
		});
		this.props.onChange(key);
	},

	_toggleOpen: function () {
		this.setState({
			open: !this.state.open
		});
	},

	render: function() {		
		var selectedText;
		var items = React.Children.map(this.props.children, function (child, index) {	
			if(!selectedText && child.props.val == this.state.selection) {				
				selectedText = child.props.children;				
			}   			
	    	return (React.addons.cloneWithProps(child, {
	    		onClick: this._handleSelection.bind(null, child.props.val)	
	    	}));            	
	    }.bind(this));

		return (
          <div className={"btn-group voting-dropdown " + (this.state.open ? 'open' : '')} onClick={this._toggleOpen}>
            <button type="button" className="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              {selectedText}
              <span className="caret"></span>
            </button>
            {this.state.open &&			
	            <ul className="dropdown-menu" role="menu">
	            	{items}
	            </ul>						
        	}
          </div>
		);
	}

});

module.exports = DropdownSelector;