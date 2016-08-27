import React from 'react';
import { connect } from 'react-redux';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import { pushState } from 'redux-router';
import { goToCategory } from '../../action-creators';
import url from '../../utils/url';
require('array.prototype.find');

const CategoryDropdown = ({
	categories, 
	onItemSelected, 
	selectedCategory,
	initialised	
}) => {
	if(!initialised) return <div />;

	const children = [
		<DropdownItem key='all' eventKey={null}>All Categories</DropdownItem>,
		<DropdownItem key='divider' eventKey='divider' divider />
	].concat(categories.map(cat =>(
		<DropdownItem eventKey={cat.id} key={cat.id}>{cat.title}</DropdownItem>
	)));
	
	const selected = selectedCategory && categories.find(cat => cat.id == selectedCategory.id);
	const selectedText = selected ? selected.title : 'All Categories';

	return (
		<Dropdown className="voting-dropdown" onItemSelected={onItemSelected} selectedText={selectedText}>
			{children}
		</Dropdown>
	);
};

export default connect (
	state => ({
		...state.categories
	}),
	{
		onItemSelected: goToCategory
	}
)(CategoryDropdown);