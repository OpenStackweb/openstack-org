import React from 'react';
import ContactCard from '../ui/ContactCard';
import CollapsableContent from '../ui/CollapsableContent';

const PresentationSpeakers = ({
	speakers,
	perRow
}) => {
	const groupedChildren = [];
	const colSize = Math.floor(12/perRow);
	let currentRow = [];	
	speakers.forEach(s => {
		currentRow.push(
			<ContactCard
				key={s.id}				
				name={`${s.first_name} ${s.last_name}`}
				title={s.title}
				bio={s.bio}
				email={s.email}
				imageURL={s.photo_url}
				twitter={s.twitter_name}
                available_for_bureau={s.available_for_bureau}
                profile_link={s.profile_link}
                avg_rate_width={s.avg_rate_width}
                is_moderator={s.is_moderator}
				/>

		);

		if(currentRow.length === perRow) {
			groupedChildren.push(currentRow);
			currentRow = [];
		}
	});

	return (
		<div>
		{groupedChildren.map((row,i) => (
			<div className="row" key={i}>
				{row.map((item,i) => (
					<div key={i} className={`col-lg-${colSize}`}>
						<CollapsableContent collapsedHeight={300}>{item}</CollapsableContent>
					</div>
				))}
			</div>
		))}
		</div>
	)
}

PresentationSpeakers.defaultProps = {
	perRow: 1
};

PresentationSpeakers.propTypes = {
	speakers: React.PropTypes.array.isRequired
};

export default PresentationSpeakers;