import React from 'react';

class ButtonGroup extends React.Component {

	constructor (props) {
		super(props);
		this.handleSelect = this.handleSelect.bind(this);
	}

	handleSelect(key) {		
		this.props.onSelect && this.props.onSelect(key);
	}

	render() {
		return (
			<div className="btn-group">
			{React.Children.map(this.props.children, child => (
				child && React.cloneElement(
					child, 
					{
						onClick: this.handleSelect,
						active: this.props.activeKey && (child.props.eventKey === this.props.activeKey)
					},
					child.props.children
				)
			))}
			</div>                  	
		);
	}
}

ButtonGroup.propTypes = {
	activeKey: React.PropTypes.any,
	onSelect: React.PropTypes.func
};

export default ButtonGroup;