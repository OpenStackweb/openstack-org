import React from 'react';
import cx from 'classnames';

class HotKeyOption extends React.Component {


	constructor (props) {
		super(props);
		this.selectOption = this.selectOption.bind(this);
		this.handleKeyup = this.handleKeyup.bind(this);
	}


	componentDidMount () {
		document.addEventListener('keyup', this.handleKeyup);
	}


	componentWillUnmount () {
		document.removeEventListener('keyup', this.handleKeyup);
	}


	handleKeyup (e) {
		if(this.props.keyCodes.indexOf(e.keyCode) !== -1) {
			this.selectOption();
		}
	}
	selectOption () {
		const { onSelected, eventKey } = this.props;
		onSelected && onSelected(eventKey);
	}


	render () {
		let classes = [
			this.props.className,
			this.props.selected ? 'current-vote' : ''
		].join(' ');
		return (
		   <li className={classes}>
		      <a href="javascript:void(0);" onClick={this.selectOption}>
		         {this.props.children}
		         <div className="voting-shortcut">{this.props.eventKey}</div>
		      </a>
		   </li>
		);
	}
}

HotKeyOption.propTypes = {
	eventKey: React.PropTypes.any.isRequired,
	keyCodes: React.PropTypes.array,
	onSelected: React.PropTypes.func,
	selected: React.PropTypes.bool
};

export default HotKeyOption;