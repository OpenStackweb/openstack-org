import React from 'react';

export default ({
	title,
	className,
	onClick
}) => {
	return (
		<li className={className}>
			<a onClick={onClick}>
				{title}			
			</a>
		</li>
	);
}
