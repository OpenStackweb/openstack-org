import React from 'react';
import {connect} from 'react-redux';
import CategoryDropdown from '../containers/CategoryDropdown';
import ListList from '../views/ListList';
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
	}

    render () {
    	if(!this.props.lists) {
    		return <div>loading</div>
    	}

        return (
            <div>
               <div className="col-lg-4">
                  <div className="ibox float-e-margins">
                  	<CategoryDropdown />
	                <ListList lists={this.props.lists} category={this.props.category} />
	               </div>
                </div>
                <div className="col-lg-8">
               		{this.props.children}
                </div>
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