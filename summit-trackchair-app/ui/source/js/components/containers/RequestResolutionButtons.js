import React from 'react';
import {connect} from 'react-redux';
import {postResolveRequest} from '../../actions';
class RequestResolutionButtons extends React.Component {

	constructor(props) {
		super(props);
		this.approve = this.approve.bind(this);
		this.reject = this.reject.bind(this);
	}

	approve(e) {
		e.preventDefault();
		this.props.resolveRequest(
			this.props.request,
			1
		);
	}

	reject(e) {
		e.preventDefault();
		this.props.resolveRequest(
			this.props.request,
			0
		);
	}

	render () {
		return (
			<div className="resolution-buttons btn-group">
				<button onClick={this.approve} className="btn btn-xs btn-success">Approve</button>
				<button onClick={this.reject} className="btn btn-xs btn-danger">Reject</button>
			</div>
		);
		
	}
}

export default connect(
	state => ({

	}),

	dispatch => ({
		resolveRequest(requestID, approved) {
			dispatch(postResolveRequest(requestID, approved));
		}
	})
)(RequestResolutionButtons);