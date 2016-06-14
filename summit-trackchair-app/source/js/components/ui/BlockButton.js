import React from 'react';

export default ({
	onButtonClicked,
	className,
	children
}) => {
	if(!className) {
		className = 'btn btn-block btn-outline btn-primary'
	}
	
	return (
		<div className="row">
			<div className="col-sm-12">
				<button onClick={onButtonClicked} className={className}>{children}</button>		
			</div>
		</div>
	);
};