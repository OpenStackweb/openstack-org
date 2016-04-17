import React from 'react';
import ensureChild from '../../utils/ensureChild';

const GalleryPanel = ({
	className,
	title,
	children
}) => (
	<div className={className} style={{clear:'both'}}>
		<h4>{title}</h4>
		<div className="items">
			{children}
		</div>
	</div>	
);


export default GalleryPanel;