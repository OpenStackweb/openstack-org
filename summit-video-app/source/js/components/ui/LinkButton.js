import React from 'react';
import cx from 'classnames';

class LinkButton extends React.Component {

	constructor (props) {
		super(props);
		this.handleClick = this.handleClick.bind(this);
	}

	handleClick (e) {
		e.preventDefault();
		this.props.onLinkClicked && this.props.onLinkClicked(
			this.props.eventKey || this.props.link
		);
		this.props.onClick && this.props.onClick();
	}

	render () {
		return (
			<a href={this.props.link} 
			   onClick={this.handleClick}
			   className={cx({active: this.props.active}), this.props.className }
			>
				{this.props.children}
			</a>
		);
	}
}

LinkButton.propTypes = {
	onLinkClicked: React.PropTypes.func,
	link: React.PropTypes.string,
	eventKey: React.PropTypes.any
};

export default LinkButton;