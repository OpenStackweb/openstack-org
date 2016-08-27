import React from 'react';
import Comment from '../ui/Comment';
import {connect} from 'react-redux';

const PresentationCommentList = ({
	comments	
}) => (
	<div className="presentation-comment-list">
		{comments.map(c => (
			<Comment
				key={c.id}
				author={c.author}
				date={c.date}
				ago={c.ago}
				comment={c.comment} 
				/>
		))}
	</div>
);

export default connect(
	state => ({
		comments: state.presentations.selectedPresentation.all_comments
	})
)(PresentationCommentList);