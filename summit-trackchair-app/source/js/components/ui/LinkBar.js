import React from 'react';

const LinkBar = ({
	onLinkClicked, 
	children,
	activeLink,
	className,
	component
}) => {
	let navChildren = children;
	const listItem = (typeof component === 'string' && component.toUpperCase() === 'UL');
	return React.createElement(
		component,
		{className},
		navChildren.map((c,i) => (
			React.cloneElement(c, {
				onLinkClicked,
				listItem, 
				active: c.props.link === activeLink.split('/')[0],
				key: i
			}, c.props.children)
		))
	);

};

LinkBar.propTypes = {
	onLinkClicked: React.PropTypes.func,
	activeLink: React.PropTypes.string
};

LinkBar.defaultProps = {
	component: 'div'
}

export default LinkBar;