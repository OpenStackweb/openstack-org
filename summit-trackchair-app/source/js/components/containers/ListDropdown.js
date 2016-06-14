import React from 'react';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {connect} from 'react-redux';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

const ListDropdown = ({
	lists,
	list,
	selectedText,
	goToList
}) => (
	<Dropdown onItemSelected={goToList} selectedText={selectedText} className="list-dropdown">
		{lists.map(l => (
			<DropdownItem eventKey={l.id} key={l.id}>{l.list_name}</DropdownItem>
		))}
	</Dropdown>
);

export default connect(
	(state, ownProps) => {
		let lists = state.lists.results || [];
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