import React from 'react';
import {connect} from 'react-redux';
import {postComment} from '../../actions';

class PresentationCommentForm extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			value: null
		}

		this.updateValue = this.updateValue.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	updateValue(e) {
		this.setState({
			value: e.target.value
		});
	}

	handleSubmit(e) {
		e.preventDefault();
		this.props.postComment(
			this.props.presentation.id, 
			{
				body: this.state.value,
				name: window.TrackChairAppConfig.userinfo.name			
			}
		);
	}

	render() {
		return (
	        <div className="chat-form">
	           <form role="form" onSubmit={this.handleSubmit}>
	              <div className="form-group">
	                 <textarea value={this.state.value} onChange={this.updateValue} className="form-control" placeholder="Write a comment..."></textarea>
	              </div>
	              <div className="text-right">
	                 <button type="submit" className="btn btn-sm btn-primary m-t-n-xs"><strong>Post comment</strong></button>
	              </div>
	           </form>
	        </div>
	    );
	}
}

export default connect (
	state => ({
		presentation: state.detailPresentation
	}),
	dispatch => ({
		postComment(presentationID, data) {
			dispatch(postComment(presentationID, data));
		}
	})
)(PresentationCommentForm);