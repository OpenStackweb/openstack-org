import React from 'react';

class TableColumn extends React.Component {
	
	render () {
		throw new Error('<TableColumn /> should never render');
	}
}

TableColumn.defaultProps = {
	sortFunc: (a,b) => (a < b ? -1 : (a > b ? 1 : 0)),
	sortable: true,
	cell: (data) => data
};

TableColumn.propTypes = {
	columnIndex: React.PropTypes.number,
	columnKey: React.PropTypes.any,
	sortable: React.PropTypes.bool,
	sortDir: React.PropTypes.oneOf([null, 1, -1]),
	cell: React.PropTypes.func.isRequired
}

export default TableColumn;