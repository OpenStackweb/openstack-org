import React from 'react';
import {connect} from 'react-redux';
import {moveSelectionUp, moveSelectionDown} from '../../actions';
import LeaderboardItem from '../ui/LeaderboardItem';
import Sortable from '../mixins/Sortable';

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

	handleUp(key) {
		this.props.onUp(this.props.list.id, key);
	}

	handleDown(key) {
		this.props.onDown(this.props.list.id, key);
	}

	updateDragState(obj) {
		//this.setState(obj);
	}

	render() {
		if(!this.props.list) return <div>Not found</div>;
		
		let {selections} = this.props.list;
		selections = selections || [];
		selections.sort((a,b) => +a.order-+b.order);

		const {can_edit} = this.props.list;
		const SortableWrapper = Sortable('div');

		return (
		<div className="selections-list">
			{selections.map((s,i) => (
				<SortableWrapper
					key={s.id}
					items={selections}
					item={s}
					sortId={i}
					outline="list"
					updateState={this.updateDragState}
					>

					<LeaderboardItem						
						title={s.title}
						rank={s.order}
						notes="Fill this in later"
						onUp={this.handleUp}
						onDown={this.handleDown}
						eventKey={s.id}
						canUp={can_edit && i > 0}
						canDown={can_edit && i < (selections.length-1)}
						/>

				</SortableWrapper>
			))}
		</div>
		);
	}	
}

export default connect(
	state => {
		return {
	
		}
	},
	dispatch => ({
		onUp(listID, selectionID) {
			dispatch(moveSelectionUp({listID, selectionID}))
		},
		onDown(listID, selectionID) {
			dispatch(moveSelectionDown({listID, selectionID}))
		},

	})
)(SelectionsList);