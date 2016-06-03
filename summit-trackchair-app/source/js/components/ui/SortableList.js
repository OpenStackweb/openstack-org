import React from 'react'

/**
 * Sortable List module
 * A sortable list component using html5 drag and drop api.
**/

class SortableList extends React.Component {

	constructor(props) {		
		super(props);
		this.placeholder = document.createElement(props.childComponent);
		this.placeholder.className = "placeholder";
		this.dragStart = this.dragStart.bind(this);
		this.dragEnd = this.dragEnd.bind(this);
		this.dragOver = this.dragOver.bind(this);
	}

  	dragStart(e) {
    	this.dragged = e.currentTarget;
    	e.dataTransfer.effectAllowed = 'move';
    	e.dataTransfer.setData("text/html", e.currentTarget);
  	}

	dragEnd(e) {
		this.dragged.style.display = "block";
		//this.dragged.parentNode.removeChild(this.placeholder);
		let data = this.props.data;
		let from = Number(this.dragged.dataset.id);
		let to = Number(this.over.dataset.id);
		if(from < to) to--;
		if(this.nodePlacement == "after") to++;
		data.splice(to, 0, data.splice(from, 1)[0]);
		this.props.onUpdate(data);
	}
	/** 
	* On drag over, update items.
	**/
	dragOver(e) {
		e.preventDefault();
		this.dragged.style.display = "none";
		if(e.target.className == "placeholder") return;
		this.over = e.target;
		let relY = e.clientY - this.over.offsetTop;
		let height = this.over.offsetHeight / 2;
		let parent = e.target.parentNode;
console.log(parent);
		if(relY > height) {
		  this.nodePlacement = "after";
		  parent.insertBefore(this.placeholder, e.target.nextElementSibling);
		}
		else if(relY < height) {
		  this.nodePlacement = "before"
		  parent.insertBefore(this.placeholder, e.target);
		}
	}

	render() {
		const listItems = this.props.data.map((item, i) => (
	      <this.props.childComponent className="react-sortable" data-id={i}
	          key={i}
	          draggable="true"
	          onDragEnd={this.dragEnd}
	          onDragStart={this.dragStart}>
	        {item}
	      </this.props.childComponent>
		));

	return <this.props.parentComponent onDragOver={this.dragOver}>{listItems}</this.props.parentComponent>
	}

}

SortableList.defaultProps = {
	childComponent: 'li',
	parentComponent: 'ul'
}

export default SortableList;