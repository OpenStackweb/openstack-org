var React = require('react');

var HotkeyButton = React.createClass({

	_keyListener: function (e) {
		if(this.props.hotkeys && this.props.hotkeys.length) {
			this.props.hotkeys.forEach(function(code) {
				if(e.keyCode == code) {
					this.props.onChange();
				}
			}.bind(this));
		}
	},


	componentDidMount: function() {
		document.addEventListener('keyup', this._keyListener);
	},


	componentDidUnmount: function () {
		document.removeEventListener('keyup', this._keyListener);
	},

	render: function() {
		return (
          <li className={"voting-rate-single " + (this.props.selected ? "active" : "")} onClick={this.props.onChange}>
            <a>
              {this.props.children}
              <div className="voting-shortcut">{this.props.hotkeyDescription}</div>
            </a>
          </li>
		);
	}

});

module.exports = HotkeyButton;