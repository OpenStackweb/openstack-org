import React from 'react';
import {connect} from 'react-redux';
import {postReorganise} from '../../actions';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import SortableLeaderboardItem from '../containers/SortableLeaderboardItem';
import ListPlaceholder from '../containers/ListPlaceholder';
import URL from '../../utils/url';

class SelectionsList extends React.Component {

	constructor (props) {
		super(props);
		this.handleUp = this.handleUp.bind(this);
		this.handleDown = this.handleDown.bind(this);
		this.handleDrag = this.handleDrag.bind(this);
		this.handleAddToTeam = this.handleAddToTeam.bind(this);
		this.handleRemove = this.handleRemove.bind(this);		
	}

	handleUp(currentIndex) {
		let newIndex = currentIndex-1;
		let {selections} = this.props;
		if(newIndex < 0) {
			newIndex = currentIndex;
		}

		this.props.reorganiseSelections(
			this.props.list.id,
			this.props.column,
			selections.move(currentIndex, newIndex)
		);
	}

	handleDown(currentIndex) {
		let newIndex = currentIndex+1;
		let {selections} = this.props;

		if(newIndex >= (selections.length-1)) {
			newIndex = currentIndex;
		}
		
		this.props.reorganiseSelections(
			this.props.list.id,
			this.props.column,
			selections.move(currentIndex, newIndex)			
		);
	}

	handleDrag(item, fromList, fromIndex, toList, toIndex) {
		if(toList === fromList) {				
			this.props.reorganiseSelections(
				this.props.list.id,
				this.props.column,
				this.props.selections.move(fromIndex, toIndex)
			);
		}
		else if(toList === this.props.column && !this.props.acceptNew) {			
			return;
		}
		else {
			this.props.onColumnChange(item, fromList, fromIndex, toList, toIndex);
		}
	}

	handleAddToTeam(id, index) {
		const item = this.props.selections.find(s => +s.id === +id);
		const existing = this.props.teamList.selections.some(s => +s.id === +id);
		if(item && !existing) {
			this.props.reorganiseSelections(
				this.props.teamList.id,
				'team',
				[
					...this.props.teamList.selections,
					item
				]
			);
		}
	}

	handleRemove(id, index) {
		this.props.reorganiseSelections(
			this.props.list.id,
			this.props.column,
			this.props.selections.filter(s => +s.id !== +id)
		);
	}

	render() {
		if(!this.props.list) return <div>Not found</div>;
		
		let {selections} = this.props;
		selections = selections || [];
		selections.sort((a,b) => +a.order-+b.order);
		const {can_edit} = this.props.list;
		const altThreshold = this.props.list.slots - this.props.list.alternates;

		return (
			<div className="selections-list">
				{selections.length === 0 && 
					<ListPlaceholder 
						onMove={this.handleDrag}
						column={this.props.column}
						/>
				}
				{selections.map((s,i) => (
				<SortableLeaderboardItem 
					key={s.id} 
					onMove={this.handleDrag}
					onRemove={this.handleRemove}
					onAddToTeam={this.handleAddToTeam}
					index={i} 
					id={s.id}
					presentation={s.presentation}
					isAlternate={i >= altThreshold}
					rank={i >= altThreshold ? 'ALT' : ('#' + s.order)}
					column={this.props.column}
					onUp={this.handleUp}
					onDown={this.handleDown}
					eventKey={i}
					canUp={(s.order > 1 && selections.length > 1)}
					canDown={(s.order < selections.length)}
					showRank={this.props.showRank}
					link={URL.create(`browse/${s.id}`, {category: this.props.category})}
					/>	
				))}
			</div>
		);
	}	
}

SelectionsList.defaultProps = {
	acceptNew: true
};

const SortableSelectionsList = DragDropContext(HTML5Backend)(SelectionsList);
export default connect(
	state => ({
		teamList: state.lists.results.find(l => l.list_type ==='Group'),
		category: state.routing.locationBeforeTransitions.query.category
	}), 
	dispatch => ({
		reorganiseSelections(listID, collection, newOrder) {
			dispatch(postReorganise(listID, collection, newOrder));
		}

	})
)(SortableSelectionsList);