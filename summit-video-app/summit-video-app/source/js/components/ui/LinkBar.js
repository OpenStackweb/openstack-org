import React from 'react';
import ensureChild from '../../utils/ensureChild';

const LinkBar = ({
	onLinkClicked, 
	children,
	activeLink,
	className
}) => (
	<div className={className}>
		{children.map((c,i) => (
			React.cloneElement(c, {
				onLinkClicked,
				active: c.props.link === activeLink.split('/')[0],
				key: i
			}, c.props.children)
		))}
	</div>        
);

LinkBar.propTypes = {
	onLinkClicked: React.PropTypes.func,
	children: ensureChild('LinkButton', 'LinkBar'),
	activeLink: React.PropTypes.string
};

export default LinkBar;