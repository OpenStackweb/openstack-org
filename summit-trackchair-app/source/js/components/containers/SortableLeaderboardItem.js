import React, { Component, PropTypes } from 'react';
import { findDOMNode } from 'react-dom';
import { DragSource, DropTarget } from 'react-dnd';
import LeaderboardItem from '../ui/LeaderboardItem';

const style = {
  cursor: 'move'
};

const cardSource = {
  beginDrag(props) {
    return {
      id: props.id,
      index: props.index,
      listID: props.list.id
    };
  }
};

const cardTarget = {
  hover(props, monitor, component) {
    const dragIndex = monitor.getItem().index;
    const hoverIndex = props.index;
    const dragList = monitor.getItem().listID;
    const hoverList = props.list.id;

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
    props.onMove(dragIndex, hoverIndex, dragList, hoverList);
    monitor.getItem().index = hoverIndex;
    monitor.getItem().listID = hoverList;
  }
};

class SortableLeaderboardItem extends Component {

  render() {
    const {
    	children,
    	isDragging,
    	connectDragSource,
    	connectDropTarget,
    	connectDragPreview 
    } = this.props;
    const opacity = isDragging ? 0.5 : 1;

    return connectDragSource(connectDropTarget(
      <div style={{ ...style, opacity }}>
        <LeaderboardItem {...this.props} />
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
    onMove: PropTypes.func.isRequired
};

const LeaderboardItemDropTarget = DropTarget('CARD', cardTarget, connect =>({
	connectDropTarget: connect.dropTarget()
}))(SortableLeaderboardItem);

export default DragSource('CARD', cardSource, (connect, monitor) => ({
  connectDragSource: connect.dragSource(),
  isDragging: monitor.isDragging()
}))(LeaderboardItemDropTarget);