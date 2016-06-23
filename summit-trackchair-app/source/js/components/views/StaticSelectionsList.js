import React from 'react';
import LeaderboardItem from '../ui/LeaderboardItem';

class StaticSelectionsList extends React.Component {

	render() {
		let {selections} = this.props;
		selections = selections || [];
		selections.sort((a,b) => +a.order-+b.order);		

		return (
			<div className="selections-list static">
				{selections.map((s,i) => (
				<LeaderboardItem 
					key={s.id} 					
					title={s.title}
					rank={s.order}					
					canUp={false}
					canDown={false}
					/>	
				))}
			</div>
		);
	}	
}

export default StaticSelectionsList;