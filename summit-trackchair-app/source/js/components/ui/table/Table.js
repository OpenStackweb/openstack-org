import React from 'react';
import TableHeading from './TableHeading';
import TableCell from './TableCell';
import TableRow from './TableRow';
import './datatables.css';

const createRow = (row, columns) => {
	const isArray = Array.isArray(row);

	return columns.map((col,i) => (
		<TableCell key={i}>
			{col.props.cell(isArray ? row[i] : row)}
		</TableCell> 
	));
};

const Table = (props) => (
	<table {...props}>
		<thead>
			<tr>
			{props.children.map((col,i) => (
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
			))}
			</tr>
		</thead>
		<tbody>
		{props.data.map((row,i) => {
			if(row.length !== props.children.length) {
				throw new Error(`Data at row ${i} is ${row.length}. It should be ${props.children.length}.`);
			}
			return (
				<TableRow even={i%2 === 0} key={i}>					
					{createRow(row, props.children)}
				</TableRow>
			);
		})}
		</tbody>
	</table>
);

export default Table;