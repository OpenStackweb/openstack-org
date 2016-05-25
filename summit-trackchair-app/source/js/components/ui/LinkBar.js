import React from 'react';

const LinkBar = ({
	onLinkClicked, 
	children,
	activeLink,
	className,
	component
}) => {
	let navChildren = children;
	if(typeof component === 'string' && component.toUpperCase() === 'UL') {
		console.log('yup');
		navChildren = children.map(c => <li>{c}</li>);
	}

	return React.createElement(
		component,
		{className},
		navChildren.map((c,i) => (
			React.cloneElement(c, {
				onLinkClicked,
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