import React from 'react';

class ButtonOption extends React.Component {

	constructor(props) {
		super(props);
		this.handleClick = this.handleClick.bind(this);
	}

	handleClick(e) {
		e.preventDefault();
		this.props.onClick && this.props.onClick(this.props.eventKey);
	}

	render() {
		const {active, children} = this.props;

		return (
			<button onClick={this.handleClick} className={`btn btn-white ${this.props.className} ${active ? 'active' : ''}`} disabled={this.props.disabled} type="button">
				{children}
			</button>
		);

	}
}

ButtonOption.propTypes = {
	active: React.PropTypes.bool,
	disabled: React.PropTypes.bool
};

export default ButtonOption;