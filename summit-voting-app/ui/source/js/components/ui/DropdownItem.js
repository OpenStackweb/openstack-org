import React from 'react';

const DropdownItem = ({
	divider,
	eventKey,
	children,
	onItemClick
}) => {
	return divider ? <li className="divider" /> : (
		<li onClick={onItemClick}>
			<a>{children}</a>
		</li>
	);
};
DropdownItem.propTypes = {
	divider: React.PropTypes.bool,
	onItemClick: React.PropTypes.func
}

export default DropdownItem;