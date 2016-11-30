import React from 'react';
import SearchForm from '../containers/SearchForm';
import CategoryDropdown from '../containers/CategoryDropdown';
import PresentationList from '../containers/PresentationList';
import { connect } from 'react-redux';
import Config from '../../utils/Config';

const Sidebar = ({

}) => (
	<div className="col-lg-3 col-md-3 col-sm-3 voting-sidebar">
		<div className="voting-app-details-link">
			<a href={Config.get('summitLink')}>More About The {Config.get('summitTitle')} Summit</a>
		</div>
		<SearchForm />
		<CategoryDropdown />
		<PresentationList />
	</div>
);

export default Sidebar;