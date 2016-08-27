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
		const link = (
			<a href={this.props.link} 
			   onClick={this.handleClick}
			   className={cx({active: this.props.active})}
			>
				{this.props.children}
			</a>
		);

		return this.props.listItem ? <li>{link}</li> : link;
	}
}

LinkButton.propTypes = {
	onLinkClicked: React.PropTypes.func,
	link: React.PropTypes.string,
	eventKey: React.PropTypes.any,
	listItem: React.PropTypes.bool
};

export default LinkButton;