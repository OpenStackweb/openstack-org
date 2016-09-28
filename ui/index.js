import ReactDOM from 'react-dom';
import React from 'react';

export default {
	mount (component, componentName) {
		document.addEventListener('DOMContentLoaded', () => {			
			const nodes = document.querySelectorAll(`[data-component=${componentName}]`);
			const nodeList = [].slice.call(nodes);
			nodeList.forEach(el => {
				const props = {};
				const attrList = [].slice.call(el.attributes);
				attrList
					.filter(attr => (
						/^data-/.test(attr.name) &&
						attr.name !== 'data-component'
					)).forEach(attr => {		    
				        const camelCaseName = attr.name
				        	.substr(5)
				        	.replace(/-(.)/g, ($0, $1) => $1.toUpperCase());

				        let propValue = attr.value;
				        if(attr.value === '' || attr.value === 'true') {
				        	propValue = true;
				        }
				        if(attr.value === 'false') {
				        	propValue = false;
				        }

				        props[camelCaseName] = propValue;

					});
				const componentName = el.getAttribute('data-component'); 
				props.children = el.textContent;

			    ReactDOM.render(
			    	React.createElement(component, props),
			    	el
			    );
			});
		});
	}
}
