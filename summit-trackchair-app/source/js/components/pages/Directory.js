import React from 'react';
import {Table, TableColumn} from '../ui/table';
import {connect} from 'react-redux';
import {sortDirectory, searchDirectory, toggleAddChair} from '../../actions';
import AddChairForm from '../containers/AddChairForm';

class Directory extends React.Component {

    render () {
        return (
			<div className="col-lg-12">
			   <div className="ibox float-e-margins">
			      <div className="ibox-content">
			         <div className="table-responsive">
			         	{this.props.directory &&
			            <div className="dataTables_wrapper form-inline dt-bootstrap">
			            {this.props.isAdmin &&
							<div className="html5buttons">
								<div className="dt-buttons btn-group">
									<a href='/trackchairs/api/v1/export/chairs' className="btn btn-default buttons-html5">
										<span><i className="fa fa-download" /> Export CSV</span>
									</a>
									<a onClick={this.props.toggleAddChair} className="btn btn-primary">
										<span><i className="fa fa-plus" /> Add new chair</span>
									</a>

								</div>
							</div>
						}
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
			            {this.props.showAddForm &&
			            	<AddChairForm />
			            }
			               <Table 
			               		sortCol={this.props.sortCol} 
			               		sortDir={this.props.sortDir} 
			               		onSort={this.props.sortTable} 
			               		data={this.props.directory} 
			               		className="table table-striped table-bordered table-hover dataTable" 
			               		role="grid"
			               	>
				               		<TableColumn columnKey='category' width='33%'>Category</TableColumn>
				               		<TableColumn columnKey='name' width='40%'>Name</TableColumn>
				               		<TableColumn
				               			columnKey='email'				               			
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
		const {
			data, 
			sortCol, 
			sortDir, 
			searchTerm, 
			searchResults, 
			showAddForm			
		} = state.directory;

		return {
			directory: searchTerm ? searchResults : data,
			sortCol,
			sortDir,
			searchTerm,
			isAdmin: window.TrackChairAppConfig.userinfo.isAdmin,
			showAddForm
		}
	},
	dispatch => ({
		sortTable(index, key, dir) {
			dispatch(sortDirectory({
				sortCol: index,
				sortDir: dir
			}));
		},
		searchDirectory(e) {
			e.preventDefault();
			dispatch(searchDirectory(e.target.value));
		},
		toggleAddChair(e) {
			e.preventDefault();
			dispatch(toggleAddChair());
		}
	})
)(Directory);