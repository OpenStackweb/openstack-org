import React from 'react';

export default ({
	title,
	body
}) => (
	<div className="row">
		<div className="col-lg-12">
			<div className="ibox">
				<div className="ibox-content">
					<h3>{title}</h3>
					<div dangerouslySetInnerHTML={{__html: body}} />
				</div>
			</div>
		</div>
	</div>
);