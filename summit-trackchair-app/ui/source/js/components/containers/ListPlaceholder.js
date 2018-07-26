import React, { Component, PropTypes } from 'react';
import { findDOMNode } from 'react-dom';
import { DragSource, DropTarget } from 'react-dnd';

const listTarget = {
  hover(props, monitor, component) {
    const itemID = monitor.getItem().id;
    const dragIndex = monitor.getItem().index;    
    const dragList = monitor.getItem().column;
    const canDrop = !(monitor.getItem().column == 'team' && props.column != 'team');

    if (canDrop) {
        props.onMove(monitor.getItem(), dragList, dragIndex, props.column, 0);

        monitor.getItem().index = 0;
        monitor.getItem().column = props.column;
        monitor.getItem().targetListID = props.listID;
        monitor.getItem().targetListHash = props.listHash;
    }

  }
};

class ListPlaceholder extends Component {

  render() {
    return this.props.connectDropTarget(
      <div style={{minHeight: 100}} />
    );
  }
}

ListPlaceholder.propTypes = {
    connectDropTarget: PropTypes.func.isRequired,
    onMove: PropTypes.func.isRequired
};

export default DropTarget('CARD', listTarget, connect =>({
	connectDropTarget: connect.dropTarget()
}))(ListPlaceholder);