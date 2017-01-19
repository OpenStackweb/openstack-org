import React from 'react';

const PaginationButton = ({
	disabled,
	onPaginate,
	direction
}) => {
	const icon = <i className={`fa fa-chevron-${direction === 'next' ? 'right' : 'left'}`} />;
	if(disabled) {
		return (
			<a className={`pagination-button ${direction} disabled`} onClick={(e) => e.preventDefault()}>
				{icon}
			</a>
		);
	}

	return (
		<a className={`pagination-button ${direction}`} onClick={onPaginate}>
			{icon}
		</a>
	);
};

PaginationButton.propTypes = {
	disabled: React.PropTypes.bool,
	onPaginate: React.PropTypes.func,
	direction: React.PropTypes.oneOf(['next','prev']).isRequired
};

PaginationButton.defaultProps = {
	disabled: false
};

export default PaginationButton;