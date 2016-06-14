import React from 'react';
import {connect} from 'react-redux';
import SelectionsList from '../views/SelectionsList';

class SelectionsDetail extends React.Component {


    render () {
    	const {
    		mustHaves,
    		alternates,
    		maybes,
    		list,
    		sessions,
    		sessionLimit,
    		altLimit 
    	} = this.props;
    	
    	if(!list) {
    		return <div>loading...</div>;
    	}
        return (
        	<div className="row">
        		<div className="col-lg-4">
					<div className="wrapper wrapper-content">
					   <div className="ibox">
					      <div className="ibox-content">
					         <div className="row">
					            <div className="col-lg-12">
					            	<h3>Must-haves ({sessions.length} / {sessionLimit})</h3>
					            	<SelectionsList selections={sessions} list={list}/>
					            </div>
					         </div>
					      </div>
					   </div>
					</div>
        		</div>
        		<div className="col-lg-4">
					<div className="wrapper wrapper-content">
					   <div className="ibox">
					      <div className="ibox-content">
					         <div className="row">
					            <div className="col-lg-12">
					            	<h3>Alternates ({alternates.length} / {altLimit})</h3>
					            	<SelectionsList selections={alternates} list={list}/>
					            </div>
					         </div>
					      </div>
					   </div>
					</div>
        		</div>
        		<div className="col-lg-4">
					<div className="wrapper wrapper-content">
					   <div className="ibox">
					      <div className="ibox-content">
					         <div className="row">
					            <div className="col-lg-12">
					            	<h3>Slush pile ({maybes.length})</h3>
					            	<SelectionsList selections={maybes} list={list}/>
					            </div>
					         </div>
					      </div>
					   </div>
					</div>
        		</div>

        	</div>
        );
    }
}

export default connect(
	(state, ownProps) => {
		const category = state.summit.data.categories.find(c => (
			c.id == state.routing.locationBeforeTransitions.query.category
		));
		const list = state.lists.results.find(l => l.id == ownProps.params.id);
		const sessionLimit = +category.session_count;
		const altLimit = +category.alternate_count;

		return {
			sessions: list ? list.selections.slice(0, sessionLimit) : null,
			alternates: list ? list.selections.slice(sessionLimit, sessionLimit+altLimit) : null,
			maybes: list ? list.selections.slice(sessionLimit+altLimit) : null,
			sessionLimit,
			altLimit,
			list
		}
		
	}
)(SelectionsDetail);