import React from 'react';

export default ({
	name
}) => {
	let value;
	const parts = name.split(' ');
	if(parts.length === 1) {
		value = parts[0].substring(0).toUpperCase();
	}
	else {
		const first = parts[0];
		const last = parts.pop();

		value = `${first.charAt(0).toUpperCase()}${last.charAt(0).toUpperCase()}`;
	}

	return <span className="initials">{value}</span>
}