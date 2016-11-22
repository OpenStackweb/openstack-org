import React from 'react';

export default (link, handler) => (el, props = {}) => {	
	return link ? <a href={link} {...props} onClick={handler}>{el}</a> : el
};