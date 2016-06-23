import React from 'react';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {connect} from 'react-redux';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

const CategoryNavigator = ({
	categories,
	selectedText,
	activeCategory,
	goToCategory
}) => (
	<Dropdown onItemSelected={goToCategory} selectedText={selectedText} activeKey={activeCategory} className="category-dropdown">
		{categories.map(c => (
			<DropdownItem eventKey={c.id} key={c.id}>{c.title}</DropdownItem>
		))}
	</Dropdown>
);

export default connect(
	state => {
		const {categories} = state.summit.data;
		let selectedText = categories.filter(c => (
			c.id == state.routing.locationBeforeTransitions.query.category
		));		

		return {
			categories,
			activeCategory: selectedText.length === 1 ? selectedText[0].id : null,
			selectedText: selectedText.length === 1 ? selectedText[0].title : '--- Select a category ---' 
		}
	},

	dispatch => ({
		goToCategory(category) {			
			browserHistory.push(URL.create(undefined, {category}));
		}
	})
)(CategoryNavigator);