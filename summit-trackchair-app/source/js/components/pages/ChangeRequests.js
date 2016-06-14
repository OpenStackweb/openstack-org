import React from 'react';
import {connect} from 'react-redux';
import {fetchChangeRequests, sortChangeRequests, searchChangeRequests} from '../../actions';
import {Table, TableColumn} from '../ui/table';
class ChangeRequests extends React.Component {

	constructor (props) {
		super(props);
		this.requestMore = this.requestMore.bind(this);
		this._timeout = null;
	}

	componentDidMount() {
		this.props.fetch({
			search: this.props.search,
			sortDir: this.props.sortDir,
			sortCol: this.props.sortCol,
			page: 1
		});
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.search !== this.props.search) {

			if(this._timeout) window.clearTimeout(this._timeout);
			
			this._timeout = window.setTimeout(() => {
				this.props.fetch({
					search: nextProps.search,
					page: 1
				});
			}, 300)
		}
		if(nextProps.sortCol !== this.props.sortCol || nextProps.sortDir !== this.props.sortDir) {
			this.props.fetch({
				sortCol: nextProps.sortCol,
				sortDir: nextProps.sortDir,
				search: this.props.search,
				page: 1
			});
		}
	}

	requestMore() {
		this.props.fetch({
			page: this.props.currentPage+1,
			sortCol: this.props.sortCol,
			sortDir: this.props.sortDir,
			search: this.props.search
		});
	}

    render () {
    	if(!this.props.changeRequests) {
    		return <div>loading</div>
    	}

        return (
			<div className="col-lg-12">
			   <div className="ibox float-e-margins">
			      <div className="ibox-content">
			         <div className="table-responsive">
			         	{this.props.changeRequests &&
			            <div className="dataTables_wrapper form-inline dt-bootstrap">
			               <div className="dataTables_filter">
			               	<label>
			               		Search: 
			               		<input 
			               			value={this.props.search}
			               			onChange={this.props.searchChangeRequests}
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
			               		data={this.props.changeRequests} 
			               		className="table table-striped table-bordered table-hover dataTable" 
			               		role="grid"
			               	>
				               		<TableColumn width='45%' columnKey='presentation'>Presentation</TableColumn>
				               		<TableColumn columnKey='status'
				               			cell={(data) => {
				               				if(data === 'Completed') {
				               					return <span className="label label-primary">{data}</span>
				               				}
				               				return <span className="label label-warning">{data}</span>
				               			}}
				               			>
				               			Status
				               		</TableColumn>
				               		<TableColumn width='15%'columnKey='oldcat'>Old Category</TableColumn>
				               		<TableColumn width='15%' columnKey='newcat'>New Category</TableColumn>
				               		<TableColumn width='15%' columnKey='requester'>Requester</TableColumn>
			               </Table>
			               {this.props.hasMore &&
			               		<button className="btn btn-block btn-outline btn-primary" onClick={this.requestMore}>Load more...</button>
			               }
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
	state => ({
		changeRequests: state.changeRequests.results,
		hasMore: state.changeRequests.has_more,
		currentPage: state.changeRequests.page,
		sortCol: state.changeRequests.sortCol,
		sortDir: state.changeRequests.sortDir,
		search: state.changeRequests.search
	}),
	dispatch => ({
		fetch(params) {			
			dispatch(fetchChangeRequests(params))
		},
		sortTable(index, key, dir) {
			dispatch(sortChangeRequests({
				sortCol: key,
				sortDir: dir
			}));
		},
		searchChangeRequests(e) {
			e.preventDefault();
			dispatch(searchChangeRequests(e.target.value));
		}

	})
)(ChangeRequests);