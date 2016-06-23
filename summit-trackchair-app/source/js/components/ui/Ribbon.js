import React from 'react';

export default ({
	type,
	children
}) => (
	<div className={`ribbon ${type}`}><span>{children}</span></div>
);