import React from 'react';
import {connect} from 'react-redux';
import CategoryNavigator from '../containers/CategoryNavigator';
import ListDropdown from '../containers/ListDropdown';
import {fetchLists} from '../../actions';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';
import Bounce from '../ui/loaders/Bounce';

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
	}

    render () {
    	if(!this.props.lists) {
    		return <Bounce />
    	}

        return (
            <div className="selections">
            <div className="container-fluid selections-navigation">
                <div className="row">
					<div className="col-md-4">
						<strong>Category</strong>: <CategoryNavigator />
					</div>
					<div className="col-md-4">
						<strong>List</strong>: <ListDropdown list={this.props.params.id} autoSelect />
					</div>
                </div>
            </div>
                {this.props.children}
            </div>
        );
    }

}

export default connect(
	state => {
		return {
			lists: state.lists.results,
			category: state.routing.locationBeforeTransitions.query.category,
			defaultCategory: state.summit.data.categories.find(c => (
				c.user_is_chair
			)) || state.summit.data.categories[0]
		};
	},
	dispatch => ({
		fetch(category) {
			dispatch(fetchLists(category))
		}
	})
)(Selections);