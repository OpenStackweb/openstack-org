import React from 'react';
import Dropdown from '../ui/Dropdown';
import {connect} from 'react-redux';
import {routeActions} from 'react-router-redux';
import URL from '../../utils/url';

const CategoryDropdown = ({
	categories,
	selectedText,
	goToCategory
}) => (
	<Dropdown onItemSelected={goToCategory} selectedText={selectedText}>
		{categories.map(c => (
			<li eventKey={c.id} key={c.id}>{c.title}</li>
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
			selectedText: selectedText.length === 1 ? selectedText[0] : '--- Select a category ---' 
		}
	},

	dispatch => ({
		goToCategory(category) {
			dispatch(routeActions.push(URL.create(undefined, {category})));
		}
	})
)(CategoryDropdown);