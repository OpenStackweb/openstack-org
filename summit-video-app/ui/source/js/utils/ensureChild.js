import React from 'react';

export default (requiredType, componentName, allowContainers = true) => {
	return function (props, propName, componentName) {
		const prop = props[propName];
		let good = true;
		React.Children.forEach(prop, child => {			
			good = good && (
				(child.type.name === requiredType) || 
				(allowContainers && child.type.name === 'Connect')
			);
		});

		if(!good) {
			return new Error (
				`${componentName} requires children of only type ${requiredType}`
			);
		}
	};
};