import React from 'react';
import {connect} from 'react-redux';
import {moveSelectionUp, moveSelectionDown, postReorder} from '../../actions';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import SortableLeaderboardItem from '../containers/SortableLeaderboardItem';

class SelectionsList extends React.Component {

	constructor (props) {
		super(props);
		this.handleUp = this.handleUp.bind(this);
		this.handleDown = this.handleDown.bind(this);
		this.updateDragState = this.updateDragState.bind(this);
		this.state = {
			draggingIndex: null
		};
	}

	handleUp(currentIndex) {
		let newIndex = currentIndex-1;
		let {selections} = this.props.list;
		if(newIndex < 0) {
			newIndex = currentIndex;
		}

		this.props.sortSelections(
			this.props.list.id,
			selections.move(currentIndex, newIndex)
		);
	}

	handleDown(currentIndex) {
		let newIndex = currentIndex+1;
		let {selections} = this.props.list;
		if(newIndex >= (selections.length-1)) {
			newIndex = currentIndex;
		}

		this.props.sortSelections(
			this.props.list.id,
			selections.move(currentIndex, newIndex)			
		);
	}

	updateDragState(dragIndex, hoverIndex) {
		let {selections} = this.props.list;
		this.props.sortSelections(
			this.props.list.id,
			selections.move(dragIndex, hoverIndex)			
		);
	}

	render() {
		if(!this.props.list) return <div>Not found</div>;
		
		let {selections} = this.props;
		selections = selections || [];
		selections.sort((a,b) => +a.order-+b.order);

		const {can_edit} = this.props.list;
		return (
			<div className="selections-list">
				{selections.map((s,i) => (
				<SortableLeaderboardItem 
					key={s.id} 
					onMove={this.updateDragState} 
					index={i} 
					id={s.id}
					title={s.title}
					rank={s.order}
					list={this.props.list}
					onUp={this.handleUp}
					onDown={this.handleDown}
					eventKey={i}
					canUp={can_edit && i > 0}
					canDown={can_edit && i < (selections.length-1)}
					canUp={true}
					canDown={true}
					/>	
				))}
			</div>
		);
	}	
}
const SortableSelectionsList = DragDropContext(HTML5Backend)(SelectionsList);
export default connect(
	state => {
		return {
	
		}
	},
	dispatch => ({
		sortSelections(listID, newOrder) {
			dispatch(postReorder(listID, newOrder));
		}

	})
)(SortableSelectionsList);