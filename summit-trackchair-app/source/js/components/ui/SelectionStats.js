import React from 'react';

export default({
	selections
}) => {
	const stats = {
		'Beginner': 0,
		'Intermediate': 0,
		'Advanced': 0,
		'N/A': 0
	};
console.log(selections);
	selections.forEach(s => {
		console.log(s.presentation.level);
		const l = s.presentation.level || 'N/A';
		stats[l]++;
	});
	return (		
		<div className="selection-stats">
			<div className="row">
				<div className="col-lg-6"><strong>Beginner</strong>: {stats.Beginner}</div>
				<div className="col-lg-6"><strong>Intermediate</strong>: {stats.Intermediate}</div>
				<div className="col-lg-6"><strong>Advanced</strong>: {stats.Advanced}</div>
				<div className="col-lg-6"><strong>N/A</strong>: {stats['N/A']}</div>
			</div>
		</div>
	);
}