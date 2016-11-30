import React from 'react';
import './slide-toggle.less';

export default ({
	children,
	open
}) => (
	<div className={`animate-slide-toggle ${open ? 'open' : 'closed'}`}>{children}</div>
)