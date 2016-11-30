import React from 'react';
import {connect} from 'react-redux';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {activatePresentationFilter} from '../../actions';

const PresentationFilterDropdown = ({
	activeFilter,
	onSelect,
	category
}) => (
	<Dropdown
		onItemSelected={onSelect}
		selectedText={<i className='fa fa-filter' />}
		activeKey={activeFilter} 
		className="filter-dropdown"
	>
		<DropdownItem eventKey='all'><i className='fa fa-list' /> All</DropdownItem>
		<DropdownItem divider />
		<DropdownItem eventKey='team'><i className='fa fa-group' /> Team selections</DropdownItem>
		<DropdownItem divider />
		<DropdownItem eventKey='untouched'><i className='fa fa-exclamation-circle' /> Needs vote</DropdownItem>
		<DropdownItem eventKey='moved'><i className='fa fa-arrow-right' /> Just moved here</DropdownItem>
		<DropdownItem divider />
		<DropdownItem eventKey='voted'><i className='fa fa-check' /> Voted on (all)</DropdownItem>
		<DropdownItem eventKey='selected'><i className='fa fa-star success' /> Vote: Selected</DropdownItem>
		<DropdownItem eventKey='maybe'><i className='fa fa-thumbs-up warning' /> Vote: Interested</DropdownItem>
		<DropdownItem eventKey='pass'><i className='fa fa-thumbs-down danger' /> Vote: No thanks</DropdownItem>

	</Dropdown>
);

export default connect(
	state => ({
		category: state.routing.locationBeforeTransitions.query.category,
		activeFilter: state.presentations.filter
	}),

	dispatch => ({
		onSelect(key) {			
			dispatch(activatePresentationFilter(key));
		}
	})
)(PresentationFilterDropdown);