import React from 'react';
import {connect} from 'react-redux';
import Dropdown from '../ui/Dropdown';
import {activatePresentationFilter} from '../../actions';
import { TagCloud } from "react-tagcloud";
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

class PresentationTagCloud extends React.Component {

    constructor(props) {
        super(props);
        this.searchTag = this.searchTag.bind(this);
    }

    searchTag(tag) {
    	let {category} = this.props
        browserHistory.push( URL.create('/browse', {search: tag.value, category: category}) );
    }

    render() {
    	let {cloud_data} = this.props;

        return (
			<Dropdown className="filter-dropdown" selectedText={<i className='fa fa-cloud' />}>
				<TagCloud minSize={12}
						  maxSize={35}
						  tags={cloud_data}
						  onClick={this.searchTag}
				/>
			</Dropdown>
        );
    }
}

export default connect(
	state => ({
        cloud_data: state.presentations.cloud_data,
        category: state.routing.locationBeforeTransitions.query.category
	}),
	dispatch => ({})
)(PresentationTagCloud);