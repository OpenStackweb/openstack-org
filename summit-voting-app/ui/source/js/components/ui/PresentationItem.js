import React from 'react';
import cx from 'classnames';
import ListItem from '../ui/ListItem';

class PresentationItem extends React.Component {

	constructor (props) {
		super(props);
		this.handleClick = this.handleClick.bind(this);
	}

	handleClick (e) {
		e.preventDefault();		
		this.props.onPresentationClicked(this.props.presentation.id);
	}

	render () {
		const {presentation, selected} = this.props;

		return (
			<ListItem 
				title={presentation.title}
				className={cx({
					active: selected,
					completed: presentation.user_vote
				})}
				onClick={this.handleClick} 
			/>
		);
	}
}

PresentationItem.propTypes = {
	presentation: React.PropTypes.object.isRequired,
	selected: React.PropTypes.bool
};

export default PresentationItem;