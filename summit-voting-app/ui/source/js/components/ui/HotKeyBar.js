import React from 'react';

export default ({
	onItemSelected,
	className,
	children,
	value
}) => (
	<ul className={className}>
		{children.map(c => (
			React.cloneElement(c, {
				onSelected: onItemSelected,
				selected: c.props.eventKey == value,
				key: c.props.eventKey
			})
		))}
	</ul>
);