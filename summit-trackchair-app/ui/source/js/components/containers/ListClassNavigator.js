import React from 'react';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {connect} from 'react-redux';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

const ListClassNavigator = ({
	list_classes,
	selectedText,
	activeClass,
	goToClass
}) => (
    <Dropdown onItemSelected={goToClass} selectedText={selectedText} activeKey={activeClass} className="ptr-dropdown">
        {list_classes.map(l => (
            <DropdownItem eventKey={l.id} key={l.id}>{l.title}</DropdownItem>
        ))}
    </Dropdown>
);

export default connect(
	state => {
		const {list_classes} = state.summit.data;

		let selectedText = list_classes.filter(c => (
			c.id == state.routing.locationBeforeTransitions.query.list_class
		));

		return {
            list_classes,
            activeClass: selectedText.length === 1 ? selectedText[0].id : list_classes[0].id,
			selectedText: selectedText.length === 1 ? selectedText[0].title : list_classes[0].title
		}
	},

	dispatch => ({
		goToClass(list_class) {
			browserHistory.push(URL.update(undefined, {list_class}));
		}
	})
)(ListClassNavigator);