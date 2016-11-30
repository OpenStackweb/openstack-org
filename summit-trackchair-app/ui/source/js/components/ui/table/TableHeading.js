import React from 'react';

class TableHeading extends React.Component {

	constructor (props) {
		super(props);
		this.handleSort = this.handleSort.bind(this);
	}

	getSortClass() {
		switch(this.props.sortDir) {
			case 1:
				return 'sorting_asc';
			case -1:
				return 'sorting_desc';
			default:
				return this.props.sortable ? 'sorting' : null
		}
	}

	handleSort(e) {
		e.preventDefault();
		if(!this.props.onSort && this.props.sortable) return;

		this.props.onSort(
			this.props.columnIndex,
			this.props.columnKey,
			this.props.sortDir ? this.props.sortDir*-1 : 1,
			this.props.sortFunc
		);
	}

	render () {
		return (
			<th onClick={this.handleSort}
				className={this.getSortClass()}
				width={this.props.width}
				>
				{this.props.children}
			</th>
		);	
	}
	
}

TableHeading.propTypes = {
	onSort: React.PropTypes.func,
	sortDir: React.PropTypes.number,
	columnIndex: React.PropTypes.number,
	columnKey: React.PropTypes.any,
	sortable: React.PropTypes.bool,
	sortFunc: React.PropTypes.func
};

export default TableHeading;