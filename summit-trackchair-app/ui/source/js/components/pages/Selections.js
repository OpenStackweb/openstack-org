import React from 'react';
import {connect} from 'react-redux';
import CategoryNavigator from '../containers/CategoryNavigator';
import ListDropdown from '../containers/ListDropdown';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';
import Bounce from '../ui/loaders/Bounce';

class Selections extends React.Component {

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
            {this.props.category &&
                <div>{this.props.children}</div>
            }
            </div>
        );
    }

}

export default connect(
	state => {
		return {
			lists: state.lists.results,
			category: state.routing.locationBeforeTransitions.query.category
		};
	},
	dispatch => ({
	})
)(Selections);