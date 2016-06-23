import React, { Component, PropTypes } from 'react';
import { findDOMNode } from 'react-dom';
import { DragSource, DropTarget } from 'react-dnd';
import LeaderboardItem from '../ui/LeaderboardItem';
import PresentationMetrics from'../views/PresentationMetrics';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {connect} from 'react-redux';

const cardSource = {
  beginDrag(props) {
    return {
      id: props.id,
      index: props.index,
      presentation: props.presentation,
      order: props.rank,
      column: props.column
    };
  }
};

const cardTarget = {
  hover(props, monitor, component) {
    const itemID = monitor.getItem().id;
    const dragIndex = monitor.getItem().index;
    const hoverIndex = props.index;
    const dragList = monitor.getItem().column;
    const hoverList = props.column;
    // Don't replace items with themselves
    if (dragIndex === hoverIndex && dragList === hoverList) {
      return;
    }

    // Determine rectangle on screen
    const hoverBoundingRect = findDOMNode(component).getBoundingClientRect();

    // Get vertical middle
    const hoverMiddleY = (hoverBoundingRect.bottom - hoverBoundingRect.top) / 2;

    // Determine mouse position
    const clientOffset = monitor.getClientOffset();

    // Get pixels to the top
    const hoverClientY = clientOffset.y - hoverBoundingRect.top;

    // Only perform the move when the mouse has crossed half of the items height
    // When dragging downwards, only move when the cursor is below 50%
    // When dragging upwards, only move when the cursor is above 50%

    // Dragging downwards
    if (dragIndex < hoverIndex && hoverClientY < hoverMiddleY && dragList === hoverList) {      
      return;
    }

    // Dragging upwards
    if (dragIndex > hoverIndex && hoverClientY > hoverMiddleY && dragList === hoverList) {
      return;
    }
    // Time to actually perform the action
    props.onMove(monitor.getItem(), dragList, dragIndex, hoverList, hoverIndex);
    monitor.getItem().index = hoverIndex;
  }
};

class SortableLeaderboardItem extends Component {
  
  constructor(props) {
  	super(props);
  	this.handleSelect = this.handleSelect.bind(this);  	
  }

  
  handleSelect (key) {
  	if(key === 'remove') {
  		this.props.onRemove && this.props.onRemove(this.props.id, this.props.index);	
  	}
  	else if(key === 'team') {
  		this.props.onAddToTeam && this.props.onAddToTeam(this.props.id, this.props.index);	
  	}  	
  }


  render() {
    const {
    	children,
    	isDragging,
    	connectDragSource,
    	connectDropTarget,
    	connectDragPreview,
    	canAddTeam,
    	isAlternate
    } = this.props;
    
    const p = this.props.presentation;
    const metrics = <PresentationMetrics presentation={p} />;

    return connectDragPreview(connectDropTarget(
      <div className={'selection-container' + (isDragging ? ' dragging' : '') + (isAlternate ? ' alternate' : '')}>
      {connectDragSource(<i className="drag-handle fa fa-bars" />)}
        <LeaderboardItem {...this.props} title={p.title} notes={metrics} />
        <div className="selection-tools">
	        <Dropdown onItemSelected={this.handleSelect} selectedText={<i className="fa fa-cog" />} caret={false}>
				{canAddTeam &&
	        	<DropdownItem eventKey='team'>
	        		<i className="fa fa-group" /> Add to team list
	        	</DropdownItem>
	        	}
	        	<DropdownItem eventKey='remove'>
	        		<i className="fa fa-remove" /> Remove from this list
	        	</DropdownItem>
	        </Dropdown>	        
        </div>
      </div>
    ));
  }
}

SortableLeaderboardItem.propTypes = {
    connectDragSource: PropTypes.func.isRequired,
    connectDropTarget: PropTypes.func.isRequired,
    index: PropTypes.number.isRequired,
    isDragging: PropTypes.bool.isRequired,
    id: PropTypes.any.isRequired,
    onMove: PropTypes.func.isRequired,
    canAddTeam: PropTypes.bool
};

const LeaderboardItemDropTarget = DropTarget('CARD', cardTarget, connect =>({
	connectDropTarget: connect.dropTarget()
}))(SortableLeaderboardItem);

const LeaderboardItemDragSource = DragSource('CARD', cardSource, (connect, monitor) => ({
  connectDragSource: connect.dragSource(),
  connectDragPreview: connect.dragPreview(),
  isDragging: monitor.isDragging()
}))(LeaderboardItemDropTarget);

export default connect(
	(state, ownProps) => {
		const teamList = state.lists.results.find(l => l.list_type === 'Group');
		if(!teamList) return;

		const teamHasThis = teamList.selections.some(s => +s.id === +ownProps.id);
		const teamIsFull = teamList.selections.length >= teamList.slots;

		return {
			canAddTeam: (!teamHasThis && !teamIsFull)
		};
	}
)(LeaderboardItemDragSource);