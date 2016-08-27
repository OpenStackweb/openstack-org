import React from 'react';

export default ({
	author,
	comment,
	date,
	ago
}) => (
	<div className="comment">
		<h5>{author}</h5>
		<span className="date">{date}</span>
		<span className="ago">{ago}</span>
		<div className="comment-body" dangerouslySetInnerHTML={{__html: comment }} />
	</div>
);