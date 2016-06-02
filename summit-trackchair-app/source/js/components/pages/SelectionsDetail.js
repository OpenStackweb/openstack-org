import React from 'react';
import {connect} from 'react-redux';
import SelectionsList from '../views/SelectionsList';

class SelectionsDetail extends React.Component {


    render () {
    	const {list} = this.props;
    	
    	if(!list) {
    		return <div>loading...</div>;
    	}
        return (
			<div className="wrapper wrapper-content animated fadeInUp">
			   <div className="ibox">
			      <div className="ibox-content">
			         <div className="row">
			            <div className="col-lg-12">
			            	Viewing {list.list_name} selections
			            	<SelectionsList list={list}/>
			            </div>
			         </div>
			      </div>
			   </div>
			</div>
        );
    }
}

export default connect(
	(state, ownProps) => ({
		list: state.lists.results.find(l => l.id == ownProps.params.id)
	})
)(SelectionsDetail);