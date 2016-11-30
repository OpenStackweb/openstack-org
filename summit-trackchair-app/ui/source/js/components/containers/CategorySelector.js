import React from 'react';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {connect} from 'react-redux';

const CategorySelector = ({
	categories,
	selectedText,
	activeCategory,
	onSelect
}) => (
	<Dropdown onItemSelected={onSelect} selectedText={selectedText} activeKey={activeCategory} className="category-dropdown">
		{categories.map(c => (
			<DropdownItem eventKey={c.id} key={c.id}>{c.title}</DropdownItem>
		))}
	</Dropdown>
);

CategorySelector.PropTypes = {
	categories: React.PropTypes.array.isRequired,
	selectedText: React.PropTypes.string,
	activeCategory: React.PropTypes.number,
	onSelect: React.PropTypes.func
};

export default connect(
	(state, ownProps) => {
		const {categories} = state.summit.data;
		categories.sort((a,b) => {
			const aName = a.title.toUpperCase();
			const bName = b.title.toUpperCase();

			return aName > bName ? 1 : (aName < bName ? -1 : 0)
		});
		
		let selectedCat = categories.find(c => +c.id === +ownProps.activeCategory)

		return {
			categories,
			activeCategory: selectedCat ? selectedCat.id : null,
			selectedText: selectedCat ? selectedCat.title : '--- Select a category ---' 
		}
	}
)(CategorySelector);