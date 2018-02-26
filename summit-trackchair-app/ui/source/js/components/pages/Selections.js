import React from 'react';
import {connect} from 'react-redux';
import CategoryNavigator from '../containers/CategoryNavigator';
import ListClassNavigator from '../containers/ListClassNavigator';
import ListDropdown from '../containers/ListDropdown';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';
import Bounce from '../ui/loaders/Bounce';

class Selections extends React.Component {

    render () {
    	let {acceptedCount, alternateCount} = this.props;

        if(!this.props.lists) {
    		return <Bounce />
    	}

        return (
            <div className="selections">
            <div className="container-fluid selections-navigation">
                <div className="row">
                    <div className="col-md-3">
                        <strong>Class</strong>: <ListClassNavigator />
                    </div>
                    <div className="col-md-3">
                        <strong>List</strong>: <ListDropdown member_id={this.props.params.member_id} autoSelect />
                    </div>
					<div className="col-md-3">
						<strong>Category</strong>: <CategoryNavigator />
					</div>
                    <div className="col-md-3">
                        <strong>Count</strong>: {acceptedCount} / {alternateCount}
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
			category: state.routing.locationBeforeTransitions.query.category,
            list_class: state.routing.locationBeforeTransitions.query.list_class,
            acceptedCount: state.lists.acceptedCount,
            alternateCount: state.lists.alternateCount
		};
	},
	dispatch => ({
	})
)(Selections);