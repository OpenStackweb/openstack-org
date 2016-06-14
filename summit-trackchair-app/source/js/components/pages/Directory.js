import React from 'react';
import {Table, TableColumn} from '../ui/table';
import {connect} from 'react-redux';
import {sortDirectory, searchDirectory} from '../../actions';

class Directory extends React.Component {

    render () {
        return (
			<div className="col-lg-12">
			   <div className="ibox float-e-margins">
			      <div className="ibox-content">
			         <div className="table-responsive">
			         	{this.props.directory &&
			            <div className="dataTables_wrapper form-inline dt-bootstrap">
			               <div className="dataTables_filter">
			               	<label>
			               		Search: 
			               		<input 
			               			value={this.props.searchTerm}
			               			onChange={this.props.searchDirectory}
			               			type="search"
			               			className="form-control input-sm" 
			               			placeholder="" 
			               			/>
			               	</label>
			               </div>
			               <Table 
			               		sortCol={this.props.sortCol} 
			               		sortDir={this.props.sortDir} 
			               		onSort={this.props.sortTable} 
			               		data={this.props.directory} 
			               		className="table table-striped table-bordered table-hover dataTable" 
			               		role="grid"
			               	>
				               		<TableColumn width='33%'>Category</TableColumn>
				               		<TableColumn width='40%'>Name</TableColumn>
				               		<TableColumn				               			
				               			cell={data => <a href={`mailto:${data}`}>{data}</a>}
				               		>
				               			Email
				               		</TableColumn>
			               </Table>
			            </div>
			        	}
			         </div>
			      </div>
			   </div>
			</div>
        );
    }
}

export default connect(
	state => {
		const {data, sortCol, sortDir, searchTerm, searchResults} = state.directory;

		return {
			directory: searchTerm ? searchResults : data,
			sortCol,
			sortDir,
			searchTerm
		}
	},
	dispatch => ({
		sortTable(index, dir) {
			dispatch(sortDirectory({
				sortCol: index,
				sortDir: dir
			}));
		},
		searchDirectory(e) {
			e.preventDefault();
			dispatch(searchDirectory(e.target.value));
		}
	})
)(Directory);