import React from 'react';
import SearchForm from '../containers/SearchForm';
import CategoryDropdown from '../containers/CategoryDropdown';
import PresentationList from '../containers/PresentationList';
import { connect } from 'react-redux';
import Config from '../../utils/Config';

class Sidebar extends React.Component {

	render () {
		return (
			<div className="voting-sidebar">
				<div className="voting-app-details-link">
					<a href={Config.get('summitLink')}>More About The {Config.get('summitTitle')} Summit</a>
				</div>
				<SearchForm />
				<CategoryDropdown />
				<PresentationList filter={this.props.filter} />
			</div>
		);		
	}
}

export default Sidebar;