import React from 'react';

export default ({
	onButtonClicked,
	className,
	children
}) => {	
	return (
		<button onClick={onButtonClicked} className={`btn btn-block btn-outline btn-primary ${className}`}>{children}</button>		
	);
};