import React from 'react';
import {connect} from 'react-redux';
import {fetchChangeRequests, sortChangeRequests, searchChangeRequests} from '../../actions';
import {Table, TableColumn} from '../ui/table';
import Bounce from '../ui/loaders/Bounce';
import RouterLink from '../containers/RouterLink';
import RequestResolutionButtons from '../containers/RequestResolutionButtons';

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
    		return <Bounce />
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
				               		<TableColumn width='45%' columnKey='presentation'
				               			cell={(data) => {
				               				return <RouterLink link={`browse/${data.id}`}>{data.title}</RouterLink>
				               			}}
				               			>
				               			Presentation
				               		</TableColumn>
				               		<TableColumn columnKey='status'
				               			cell={(data) => {
				               				if(data === 'Approved') {
				               					return <span className="label label-success">{data}</span>
				               				}
				               				else if(data === 'Rejected') {
				               					return <span className="label label-danger">{data}</span>	
				               				}

				               				return <span className="label label-warning">{data}</span>
				               			}}
				               			>
				               			Status
				               		</TableColumn>
				               		<TableColumn width='15%'columnKey='oldcat'>Old Category</TableColumn>
				               		<TableColumn width='15%' columnKey='newcat'>New Category</TableColumn>
				               		<TableColumn width='15%' columnKey='requester'>Requester</TableColumn>
				               		{this.props.isAdmin &&
				               			<TableColumn width='10%' columnKey='admin'
				               				cell={(data, row) => {
				               					if(data === false) {
				               						return <small>Presentation already has selections.</small>
				               					}
				               					if(row[1] === 'Pending') {
				               						return <RequestResolutionButtons request={data} />
				               					}
				               					return <span />
				               					
				               				}}
				               			>
				               				Admin
				               			</TableColumn>
				               		}
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
		search: state.changeRequests.search,
		isAdmin: state.changeRequests.results && 
				 state.changeRequests.results.length && 
				 state.changeRequests.results[0].length === 6

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