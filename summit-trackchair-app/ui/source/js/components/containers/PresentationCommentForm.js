import React from 'react';
import {connect} from 'react-redux';
import {postComment} from '../../actions';

class PresentationCommentForm extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			value: null,
            is_public: false
		}

		this.updateValue = this.updateValue.bind(this);
		this.changePublic = this.changePublic.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	updateValue(e) {
		this.setState({
			value: e.target.value
		});
	}

    changePublic(e) {
        this.setState({
            is_public: e.target.checked
        });
    }

	handleSubmit(e) {
		e.preventDefault();

		if(!this.state.value) {
			return;
		}

		this.props.postComment(
			this.props.presentation.id, 
			{
				body: this.state.value,
				is_public: this.state.is_public,
				name: window.TrackChairAppConfig.userinfo.name			
			}
		);
		this.setState({
			value: ''
		});
	}

	render() {		
		return (
	        <div className="chat-form">
	           <form role="form" onSubmit={this.handleSubmit}>
				   <div className="text-right">
					   <div className="form-check checkbox">
						   <input type="checkbox" id="is_public" checked={this.state.is_public} onChange={this.changePublic} />
						   <label htmlFor="is_public">
                               Show to Presenter
						   </label>
					   </div>
				   </div>
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