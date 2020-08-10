import React from 'react';
import {connect} from 'react-redux';
import {fetchChangeRequests, sortChangeRequests, searchChangeRequests, postResolveRequest} from '../../actions';
import Table from '../ui/table/Table';
import TableColumn from '../ui/table/TableColumn';
import Bounce from '../ui/loaders/Bounce';
import RouterLink from '../containers/RouterLink';
import RequestResolutionButtons from '../containers/RequestResolutionButtons';
import {Modal, Button, OverlayTrigger, Tooltip} from 'react-bootstrap';

class ChangeRequests extends React.Component {

	constructor (props) {
		super(props);
		this.requestMore = this.requestMore.bind(this);
        this.openModal = this.openModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
        this.handleReasonChange = this.handleReasonChange.bind(this);
        this.handleRejectionSubmit = this.handleRejectionSubmit.bind(this);
		this._timeout = null;
        this.state = {showModal: false, rejectRequestID: 0, rejectReason: ''};
	}

	componentDidMount() {
		let { summitID } = window.TrackChairAppConfig;

		this.props.fetch({
			summitID: summitID,
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

    closeModal() {
        this.setState({ showModal: false });
    }

    openModal(requestID) {
        this.setState({ showModal: true, rejectRequestID: requestID });
    }

    handleReasonChange(e) {
        this.setState({ rejectReason: e.target.value});
    }

    handleRejectionSubmit(e) {
        e.preventDefault();
        this.setState({ showModal: false });
        this.props.sendRejectionReason(this.state.rejectRequestID, this.state.rejectReason);
    }

    render () {
    	if(!this.props.changeRequests) {
    		return <Bounce />
    	}

    	console.log(this.props.changeRequests)

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
					  	<TableColumn columnKey='id'>Id</TableColumn>
                        <TableColumn width='40%' columnKey='presentation'
                            cell={(data) => {
                                return <RouterLink link={`browse/${data.id}`}>{data.title}</RouterLink>
                            }}
                            >
                            Presentation
                        </TableColumn>
                        <TableColumn columnKey='status'
                            cell={(data) => {
                                if(data.status === 'Approved') {
                                    return <span className="label label-success">{data.status}</span>
                                } else if(data.status === 'Rejected') {
                                    return (
                                        <OverlayTrigger placement="bottom" overlay={<Tooltip id="tooltip"> { data.reason } </Tooltip>}>
                                            <span className="label label-danger">{data.status}</span>
                                        </OverlayTrigger>
                                    )
                                }

                                return <span className="label label-warning">{data.status}</span>
                            }}
                            >
                            Status
                        </TableColumn>
                        <TableColumn width='12%' columnKey='oldcat'>Old Category</TableColumn>
                        <TableColumn width='12%' columnKey='newcat'>New Category</TableColumn>
                        <TableColumn width='12%' columnKey='requester'>Requester</TableColumn>
                        {this.props.isAdmin &&
                            <TableColumn width='15%' columnKey='admin'
                                cell={(data, row) => {
                                    if(row[2].status === 'Pending') {
                                        if(data == 'has_selections') {
                                            return <small>Presentation already has selections.</small>
                                        } else if (data == 'not_admin') {
                                            return <small>Not your track.</small>
                                        } else {
                                            return <RequestResolutionButtons request={data} onReject={this.openModal} />
                                        }
                                    } else {
                                        return <small>{row[2].status} by {row[2].approver}</small>
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
              <Modal show={this.state.showModal} onHide={this.closeModal}>
                <Modal.Header closeButton>
                  <Modal.Title>Track Change Rejection</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                  <p>Please state a reason why you reject this change:</p>
                  <textarea className="rejectReason" value={this.state.rejectReason} onChange={this.handleReasonChange}></textarea>
                </Modal.Body>
                <Modal.Footer>
                  <Button onClick={this.handleRejectionSubmit}>Send</Button>
                </Modal.Footer>
              </Modal>
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
				 state.changeRequests.results[0].length === 7
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
		},
        sendRejectionReason(rejectRequestID, rejectReason) {
            dispatch(postResolveRequest(rejectRequestID, 0, rejectReason));
        }

	})
)(ChangeRequests);