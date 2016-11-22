import React from 'react';
import {connect} from 'react-redux';
import {postComment} from '../../action-creators';

class CommentForm extends React.Component {

	constructor (props) {
		super(props);
		const {user_comment} = this.props.presentation;
		
		this.state = {
			comment: user_comment ? user_comment.comment.replace(/<br \/>/g, "") : null
		};
		this.updateComment = this.updateComment.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	updateComment(e) {
		this.setState({
			comment: e.target.value
		})
	}

	handleSubmit(e) {
		e.preventDefault();
		if(this.state.comment) {
			this.props.onCreateComment(
				this.props.presentation,
				this.state.comment
			);

			this.setState({
				comment: null
			});
		}
	}

	render () {		
		return (
			<form onSubmit={this.handleSubmit}>
				<textarea rows={10} className="form-control" value={this.state.comment} onChange={this.updateComment}></textarea>
				<button className="btn block-btn btn-primary" type="submit">Add comment</button>
			</form>
		);
	}

}

export default connect(
	state => ({
		presentation: state.presentations.selectedPresentation	
	}),
	dispatch => ({
		onCreateComment(presentation, comment) {
			dispatch(postComment(presentation.id, comment));
		}
	})
)(CommentForm);