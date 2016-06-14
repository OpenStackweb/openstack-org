import React from 'react';
import {connect} from 'react-redux';
import CategoryDropdown from '../containers/CategoryDropdown';
import ListDropdown from '../containers/ListDropdown';
import {fetchLists} from '../../actions';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

class Selections extends React.Component {

	componentDidMount() {
		if(this.props.category) {			
			this.props.fetch(this.props.category);
		}
		else {			
			const category = this.props.defaultCategory.id;
			browserHistory.push(URL.create(undefined, {category}));
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.category !== this.props.category) {
			this.props.fetch(nextProps.category);
		}
		if(nextProps.lists && !this.props.params.id) {
			browserHistory.push(URL.create(`selections/${nextProps.lists[0].id}`, true));
		}
	}

    render () {
    	if(!this.props.lists) {
    		return <div>loading</div>
    	}

        return (
            <div className="selections">
                <div className="row">
					<div className="col-lg-4">
						<strong>Category</strong>: <CategoryDropdown autoSelect />
					</div>
					<div className="col-lg-4">
						<strong>List</strong>: <ListDropdown list={this.props.params.id} category={this.props.category} />
					</div>
                </div>
                {this.props.children}
            </div>
        );
    }

}

export default connect(
	state => ({
		lists: state.lists.results,
		category: state.routing.locationBeforeTransitions.query.category,
		defaultCategory: state.summit.data.categories[0]		
	}),
	dispatch => ({
		fetch(category) {
			dispatch(fetchLists(category))
		}
	})
)(Selections);