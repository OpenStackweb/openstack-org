import React from 'react';

const DropdownItem = ({
	divider,
	eventKey,
	children,
	onItemClick,
	active
}) => {
	return divider ? <li className="divider" /> : (
		<li onClick={onItemClick} className={active ? 'active' : ''}>
			<a>{children}</a>
		</li>
	);
};
DropdownItem.propTypes = {
	divider: React.PropTypes.bool,
	onItemClick: React.PropTypes.func,
	eventKey: React.PropTypes.any
}

export default DropdownItem;