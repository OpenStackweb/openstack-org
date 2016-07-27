import React from 'react';
import VotingBar from '../containers/VotingBar';
import PresentationMeta from '../ui/PresentationMeta';
import VotingSpeakerRow from '../ui/VotingSpeakerRow';
import MainSpeakerRow from '../ui/MainSpeakerRow';
import CommentForm from '../containers/CommentForm';
import Comment from '../ui/Comment'
import PresentationCommentList from '../containers/PresentationCommentList';
import PresentationNotices from './PresentationNotices';
import PresentationError from './PresentationError';
import Config from '../../utils/Config';
import { connect } from 'react-redux';
import { requestPresentation, toggleCommentForm, destroyUserComment } from '../../action-creators';

require('array.prototype.find');

class PresentationDetail extends React.Component {

	_getPresentation (props) {
		props = props || this.props;

		if(
			(!props.presentation || (props.presentation.id !== this.props.presentation.id))
		) {
			this.props.requestPresentation(
				props.presentation.id
			);
		}

	}
	componentDidMount () {
		this._getPresentation();
	}

	componentWillReceiveProps (nextProps) {
		this._getPresentation(nextProps);
	}


	render () {
		const {presentation} = this.props;
		const loggedIn = Config.get('loggedIn');

		if(!presentation.speakers) return <div />;
		let showForm = false;
		if(presentation) {
			showForm = (
				presentation.user_vote !== null &&
				presentation.user_comment === null
			) || presentation.showForm;
		}

		const speakers = presentation.speakers.filter(s => !s.isModerator);
		const moderators = presentation.speakers.filter(s => s.isModerator);

		return (
			<div>
			   <a onClick={(e) => {
			   		e.preventDefault();
			   		document.body.classList.toggle('openVotingNav');
			   	}} className="voting-open-panel text">
			   		<i className="fa fa-chevron-left" /> All Submissions
			   </a>
			   {presentation &&
			   <div className="voting-content-body">
			   			{presentation.error &&
			   				<PresentationError />
			   			}
			   			{!presentation.error &&
			   			<div>
			   				<PresentationNotices loggedIn={loggedIn} votingOpen={Config.get('votingOpen')} />
							{loggedIn &&
								<div>
									<h5>Cast Your Vote</h5>
									<VotingBar presentation={presentation} />
									<div className="voting-tip">
										<strong>TIP:</strong> You can vote quickly with your keyboard using the numbers below each option.
									</div>
								</div>
							}
						  	<div>
						      	<div className="voting-presentation-body">
						      		<PresentationMeta presentation={presentation} />
							         <h5>Abstract</h5>			         
							         <div className="voting-presentation-track" dangerouslySetInnerHTML={{__html: presentation.abstract}} />						         
							         {speakers.length > 0 &&
							         	<div>
								         	<h5>Speakers</h5>
								         	<MainSpeakerRow speakers={speakers}/>
							         	</div>
							     	 }
							         {moderators.length > 0 &&
							         	<div>
								         	<h5>Moderators</h5>
								         	<MainSpeakerRow speakers={moderators}/>
							         	</div>
							     	 }

						      	</div>
						      	{loggedIn &&
							      	<div>
						      			<h5>Cast Your Vote</h5>						      	
							      		<VotingBar presentation={presentation} />
								      	<div className="voting-tip">
								        	<strong>TIP:</strong> You can vote quickly with your keyboard using the numbers below each option.
								      	</div>
							      		
							      		{presentation.user_comment &&
								      		<div className="comment-list your-comment">
								      			<h4>Your comment</h4>
								      			<Comment
								      				author={presentation.user_comment.author}
								      				comment={presentation.user_comment.comment}
								      				date={presentation.user_comment.date}
								      				ago={presentation.user_comment.ago}
								      				/>
								      			<a onClick={() => this.props.toggleCommentForm(!presentation.showForm)}>
								      				Edit
								      			</a> | 
								      			<a onClick={() => this.props.destroyUserComment(presentation.id)}>
								      				Delete
								      			</a>

								      		</div>
							      		}
							      		{presentation.all_comments && presentation.all_comments.length > 0 &&
								      		<div className="comment-list all-comments">
								      			<h4>Comments from voters</h4>
								      			<PresentationCommentList />
								      		</div>
							      		}						      		
							      		{showForm &&
							      			<div className="voting-comment">								      			
								      			<p>Leave a comment</p>
								      			<CommentForm />
								      			<small>Your vote is anonymous. Your comment is not.</small>
							      			</div>
							      		}

								    </div>
						      	}
 						    </div>
						</div>
						}		   				      
			   </div>
			   }
			</div>

		);

	}	
}

export default connect (
	state => ({
		presentation: state.presentations.selectedPresentation
	}),
	dispatch => ({
		requestPresentation(id) {
			dispatch(requestPresentation(id));
		},
		toggleCommentForm(bool) {
			dispatch(toggleCommentForm(bool));
		},
		destroyUserComment(id) {
			if(window.confirm('Delete your comment?')) {
				dispatch(destroyUserComment(id));
			}
		}
	})
)(PresentationDetail);