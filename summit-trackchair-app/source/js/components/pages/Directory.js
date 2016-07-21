import React from 'react';
import {Table, TableColumn} from '../ui/table';
import {connect} from 'react-redux';
import {sortDirectory, searchDirectory, toggleAddChair, postDeleteChair} from '../../actions';
import AddChairForm from '../containers/AddChairForm';

class Directory extends React.Component {

    render () {
    	const {
    		directory, 
    		isAdmin, 
    		toggleAddChair, 
    		searchTerm, 
    		searchDirectory, 
    		showAddForm, 
    		sortCol, 
    		sortDir, 
    		sortTable,
    		deleteRow
    	} = this.props;

   		const columns =[
   			<TableColumn columnKey='category' width='33%' cell={data => data.category}>
   				Category
   			</TableColumn>,
   			<TableColumn columnKey='name' width='40%' cell={data => data.name}>
   				Name
   			</TableColumn>,
   			<TableColumn columnKey='email' cell={data => <a href={`mailto:${data.email}`}>{data.email}</a>}>
   				Email
   			</TableColumn>
   		];

   		if(isAdmin) {
   			columns.push(
   				<TableColumn
   					sortable={false}
   					columnKey='actions'
   					width='8%'
   					cell={data => (
   						<a onClick={(e) => deleteRow(data.chair_id, data.category_id)}>(remove)</a>
   					)}
   				 />

   			)
   		}

        return (
			<div className="col-lg-12">
			   <div className="ibox float-e-margins">
			      <div className="ibox-content">
			         <div className="table-responsive">
			         	{directory &&
			            <div className="dataTables_wrapper form-inline dt-bootstrap">
			            {isAdmin &&
							<div className="html5buttons">
								<div className="dt-buttons btn-group">
									<a href='/trackchairs/api/v1/export/chairs' className="btn btn-default buttons-html5">
										<span><i className="fa fa-download" /> Export CSV</span>
									</a>
									<a onClick={toggleAddChair} className="btn btn-primary">
										<span><i className="fa fa-plus" /> Add new chair</span>
									</a>

								</div>
							</div>
						}
			               <div className="dataTables_filter">
			               	<label>
			               		Search: 
			               		<input 
			               			value={searchTerm}
			               			onChange={searchDirectory}
			               			type="search"
			               			className="form-control input-sm" 
			               			placeholder="" 
			               			/>
			               	</label>
			               </div>
			            {showAddForm &&
			            	<AddChairForm />
			            }
			               <Table 
			               		sortCol={sortCol} 
			               		sortDir={sortDir} 
			               		onSort={sortTable} 
			               		data={directory} 
			               		className="table table-striped table-bordered table-hover dataTable" 
			               		role="grid"
			               	>
			               		{columns}
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
			sortCol, 
			sortDir, 
			searchTerm, 
			searchResults, 
			showAddForm			
		} = state.directory;

		let {data} = state.directory;
		if(sortCol) {
			data = state.directory.data.sort((aObj,bObj) => {
				let a = aObj[sortCol].toUpperCase();
				let b = bObj[sortCol].toUpperCase();
				let result = (a < b ? -1 : (a > b ? 1 : 0));
				return result*sortDir;
	    	});
		}

		if(searchTerm) {
        	const rxp = new RegExp(searchTerm,'i');
        	data = data.filter(chairData => (
        		chairData.name.match(rxp) ||
        		chairData.category.match(rxp) ||
        		chairData.email.match(rxp)
        	));
		}

		return {
			directory: data,
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
				sortCol: key,
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
		},
		deleteRow(chairID, categoryID) {
			if(window.confirm('Delete this chair?')) {
				dispatch(postDeleteChair({chairID, categoryID}));	
			}
		}
	})
)(Directory);