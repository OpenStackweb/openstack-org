import React from 'react';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {connect} from 'react-redux';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

class ListDropdown extends React.Component {

	componentDidMount() {
		if(this.props.autoSelect && !this.props.lists.find(l => l.id == this.props.list)) {
			let mine = this.props.lists.find(l => l.mine);

			this.props.goToList(mine ? mine.id : this.props.lists[0].id);
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.autoSelect && !nextProps.lists.find(l => l.id == nextProps.list)) {
			let mine = nextProps.lists.find(l => l.mine);			
			nextProps.goToList(mine ? mine.id : nextProps.lists[0].id);
		}
	}

	render() {
		const {
			lists,
			list,
			selectedText,
			goToList
		} = this.props;
		
		return (
			<Dropdown onItemSelected={goToList} selectedText={selectedText} className="list-dropdown">
				{lists.map(l => (
					<DropdownItem eventKey={l.id} key={l.id}>{l.list_name}</DropdownItem>
				))}
			</Dropdown>
		);		
	}	
}

export default connect(
	(state, ownProps) => {
		let lists = state.lists.results || [];
		lists = lists.filter(l => l.list_type !== 'Group');
		let selectedText = lists.filter(l => l.id == ownProps.list);

		return {
			lists,
			selectedText: selectedText.length === 1 ? selectedText[0].list_name : '--- Select a track chair ---' 
		}
	},

	dispatch => ({
		goToList(list) {			
			browserHistory.push(URL.create(`selections/${list}`, true));
		}
	})
)(ListDropdown);