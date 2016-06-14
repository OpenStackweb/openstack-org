import React from 'react';

const TableCell = (props) => (
	<td {...props}>{props.children}</td>
);

export default TableCell;