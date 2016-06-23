import React from 'react';

const TableRow = (props) => (
	<tr role="row" className={props.even ? 'even' : 'odd'}>
		{props.children}
	</tr>
);

export default TableRow;