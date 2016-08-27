import React from 'react';
import LeaderboardItem from '../ui/LeaderboardItem';
import URL from '../../utils/url';
import {connect} from 'react-redux';
import PresentationMetrics from './PresentationMetrics';

class StaticSelectionsList extends React.Component {

	render() {
		let {selections} = this.props;
		selections = selections || [];
		const altThreshold = this.props.list.slots - this.props.list.alternates;

		return (
			<div className="selections-list static">
				{selections.map((s,i) => (
					<div key={s.id} className={'selection-container' + (i >= altThreshold ? ' alternate' : '')}>
						<LeaderboardItem 							
							title={s.presentation.title}
							rank={i >= altThreshold ? 'ALT' : ('#' + s.order)}
							showRank={this.props.showRank}
							canUp={false}							
							canDown={false}
							link={URL.create(`browse/${s.id}`, {category: this.props.category})}
							notes={<PresentationMetrics presentation={s.presentation} />}
						>
							<div className="selection-meta">{s.presentation.level}</div>
						</LeaderboardItem>	
					</div>
				))}
			</div>
		);
	}	
}

export default connect(
	state => ({
		category: state.routing.locationBeforeTransitions.query.category
	})
)(StaticSelectionsList);