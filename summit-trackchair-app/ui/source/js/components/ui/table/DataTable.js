import React from 'react';
import Table from './Table';
import TableColumn from './TableColumn';

class DataTable extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			data: this.props.data,
			sortColumn: this.props.sortColumn,
			sortDir: this.props.sortDir
		};
		this.handleSort = this.handleSort.bind(this);
	}

	handleSort (index, key, dir, func) {
		if(!func) return;

		this.setState({
			sortColumn: key || index,
			sortDir: dir,
			data: this.state.data.sort((a,b) => {
				return this.state.sortDir == 1 ?
					func(a[index], b[index]) :
					func(b[index], a[index])
			})
		});
	}

	render() {
		const {sortColumn, sortDir} = this.state;
		return (
			<Table 
				onSort={this.handleSort}
				sortCol={sortColumn}
				sortDir={sortDir}
				{...this.props} 
				data={this.state.data}
			>
				{this.props.children}
			</Table>
		);
	}
}

export default DataTable;