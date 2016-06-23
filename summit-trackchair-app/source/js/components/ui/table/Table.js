import React from 'react';
import TableHeading from './TableHeading';
import TableCell from './TableCell';
import TableRow from './TableRow';
import './datatables.css';

const createRow = (row, columns) => {
	const isArray = Array.isArray(row);

	return columns.map((col,i) => (
		<TableCell key={i}>
			{col.props.cell((isArray ? row[i] : row), (isArray ? row : undefined))}
		</TableCell> 
	));
};

const validChildren = (children) => {
	return children.filter(c => (c && c.props && (typeof c.props.cell === 'function')));
}


const Table = (props) => {
	const children = validChildren(props.children);

	return (
	<table {...props}>
		<thead>
			<tr>
			{children.map((col,i) => {								
				return (
				<TableHeading 
					onSort={props.onSort}
					sortDir={() => {
						if(col.props.columnKey && (col.props.columnKey === props.sortCol)) {
							return props.sortDir;
						}
						if(props.sortCol === i) {
							return props.sortDir;
						}
						return null
					}()}					
					sortable={col.props.sortable}
					sortFunc={col.props.sortFunc}
					columnIndex={i}
					columnKey={col.props.columnKey}
					width={col.props.width}
					key={i}
				>
					{col.props.children}
				</TableHeading>
				);
			})}
			</tr>
		</thead>
		<tbody>
		{children.length > 0 && props.data.map((row,i) => {
			if(row.length !== children.length) {
				console.warn(`Data at row ${i} is ${row.length}. It should be ${children.length}.`);
				return <tr />
			}
			return (
				<TableRow even={i%2 === 0} key={i}>					
					{createRow(row, children)}
				</TableRow>
			);
		})}
		</tbody>
	</table>
	);
};

export default Table;