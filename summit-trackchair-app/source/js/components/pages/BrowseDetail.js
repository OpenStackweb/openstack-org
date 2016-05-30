import React from 'react';
import {connect} from 'react-redux';
import Initials from '../ui/Initials';
import {fetchPresentationDetail, postMySelection, postGroupSelection} from '../../actions';
import PresentationCommentForm from '../containers/PresentationCommentForm';
import PresentationMeta from '../views/PresentationMeta';
import PresentationActivity from '../views/PresentationActivity';
import PresentationSpeakers from '../views/PresentationSpeakers';

class BrowseDetail extends React.Component {

	constructor(props) {
		super(props);
		this.toggleMySelection = this.toggleMySelection.bind(this);
		this.toggleGroupSelection = this.toggleGroupSelection.bind(this);
	}
	componentDidMount() {
		this.props.fetch(this.props.params.id);
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.params.id !== this.props.params.id) {
			this.props.fetch(nextProps.params.id);
		}
	}

	toggleMySelection() {
		this.props.toggleForMe(
			this.props.presentation.id,
			!this.props.presentation.selected
		);
	}

	toggleGroupSelection() {
		this.props.toggleForGroup(
			this.props.presentation.id,
			!this.props.presentation.group_selected
		);
	}

    render () {
    	const p = this.props.presentation;
    	
    	if(!p.id) {
    		return <div>loading...</div>;
    	}
        return (
			<div className="wrapper wrapper-content animated fadeInUp">
			   <div className="ibox">
			      <div className="ibox-content">
			         <div className="row">
			            <div className="col-lg-12">
			            {p.can_assign &&
			               <div className="row">
			                  <div className="col-lg-3 pull-right">
			                     <a onClick={this.toggleMySelection} href="#" className="btn btn-primary btn-xs">
			                     	<i className={`fa fa-${p.selected ? 'minus' : 'plus'}`}></i> My list
			                     </a>
			                     <a onClick={this.toggleGroupSelection} href="#" className="btn btn-warning btn-xs">
			                     	<i className={`fa fa-${p.group_selected ? 'minus' : 'plus'}`}></i> Team list
			                     </a>
			                  </div>
			               </div>
			            }
			               <div className="m-b-md">
			                  <h2>{p.title}</h2>
			               </div>
			            </div>
			         </div>
					<PresentationMeta presentation={p} />
					<h3>Speakers</h3>
					<PresentationSpeakers speakers={p.speakers} />					
					<PresentationActivity activity={p.comments} />
			      </div>
			   </div>
			</div>
        );
    }
}

export default connect(
	state => ({
		presentation: state.detailPresentation,		
	}),
	dispatch => ({
		fetch(id) {
			dispatch(fetchPresentationDetail(id));
		},
		toggleForMe(presentationID, bool) {
			dispatch(postMySelection(presentationID, bool));
		},
		toggleForGroup(presentationID, bool) {
			dispatch(postGroupSelection(presentationID, bool));
		}

	})
)(BrowseDetail);