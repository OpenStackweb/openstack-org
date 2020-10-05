import React from 'react';
import {connect} from 'react-redux';
import Initials from '../ui/Initials';
import {
	postMySelection, 
	postGroupSelection,
	postMarkAsRead,
    toggleEmailSpeakers,
	updateDetailPresentationID
} from '../../actions';
import PresentationCommentForm from '../containers/PresentationCommentForm';
import PresentationEmailForm from '../containers/PresentationEmailForm';
import PresentationMeta from '../views/PresentationMeta';
import PresentationTags from '../views/PresentationTags';
import PresentationActivity from '../views/PresentationActivity';
import PresentationSpeakers from '../views/PresentationSpeakers';
import SelectionButtonBar from '../containers/SelectionButtonBar';
import Ribbon from '../ui/Ribbon';
import Wave from '../ui/loaders/Wave';
import RouterLink from '../containers/RouterLink';
import {getFilteredPresentations} from '../../selectors';

class BrowseDetail extends React.Component {

	constructor(props) {
		super(props);
		this.toggleMySelection = this.toggleMySelection.bind(this);
		this.toggleGroupSelection = this.toggleGroupSelection.bind(this);
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.presentation.id && !nextProps.presentation.viewed) {
			this.props.markAsRead(nextProps.params.id);
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
    	const {selectionsRemaining, myList, isAdmin, index, total} = this.props;

    	if(!p.id || p.loading) {
    		return <Wave />
    	}

    	const ribbonTypes = {
    		'selected': 'success',
    		'maybe': 'warning',
    		'pass': 'danger'
    	};

    	const voteLookup = {
    		'maybe': 'Interested',
    		'selected': 'Selected',
    		'pass': 'No thanks'
    	};

    	const speakers = p.speakers.filter(s => !s.is_moderator);
    	const moderators = p.speakers.filter(s => !!s.is_moderator);

        return (
			<div className="wrapper wrapper-content">
				{p.selected && !p.group_selected &&
				  <Ribbon type={ribbonTypes[p.selected]}>{voteLookup[p.selected]}</Ribbon>
				}
				{p.group_selected &&
				  <Ribbon type='primary'>TEAM SELECTION</Ribbon>
				}

				<div className="presentation-utils">
					<div className="row">
						<div className="col-xs-12 col-md-8">
							<small className="keyboard-help">
								<strong>Keyboard commands</strong>:
								<ul>
									<li><i className="keyboard-key fa fa-caret-square-o-left" /> Prev presentation</li>
									<li><i className="keyboard-key fa fa-caret-square-o-right" /> Next presentation</li>
									<li><span className="keyboard-key">[Y]</span>es</li>
									<li><span className="keyboard-key">[I]</span>nterested</li>
									<li><span className="keyboard-key">[N]</span>o thanks</li>
									<li><span className="keyboard-key">[Q]</span>Clear</li>
								</ul>
							</small>
						</div>
						{(p.can_assign || isAdmin) && !!index && !!total &&
							<div className="col-xs-12 col-md-4 pull-right">
								<span className="presentation-index">
									Viewing:&nbsp;
									<strong>{this.props.index} / {this.props.total}</strong>
								</span>
							</div>
						}
					</div>					
				</div>

			   <div className="ibox">
			      <div className="ibox-content">

			         <div className="row">
			            <div className="col-lg-12">
			            {p.change_requests_count > 0 && ( isAdmin || p.can_assign ) &&
			            	<div className="alert alert-info">
			            		This presentation has {p.change_requests_count} category change requests that are unresolved.
			            		[<RouterLink link='change-requests'>View</RouterLink>]
			            	</div>
			            }
                        {p.show_comment_message && ( isAdmin || p.can_assign ) &&
                            <div className="alert alert-info">
                                New comments awaiting! Please refresh to view them.
                            </div>
                        }
			            {myList &&
			            	<p>
                                <small className="pull-right">
                                    Selections remaining: { selectionsRemaining }
                                </small>
                            </p>
			            }
			            {p.can_assign &&
			               <div className="row">
			                  <div className="col-lg-6 col-lg-offset-6 col-md-8 col-md-offset-4">
			                  	<div className="pull-right">			                  	
			                  		<SelectionButtonBar />
			                  	</div>
			                  </div>
			               </div>
			            }
			               <div className="m-b-md">
			                  <h2>{p.title}</h2>
			               </div>
			            </div>
			         </div>
					<PresentationTags tags={p.tags} />
					<PresentationMeta presentation={p} />
					<h3>Speakers</h3>
					<PresentationSpeakers speakers={speakers} />
					{moderators.length > 0 &&
					<div>
						<h3>Moderators</h3>
						<PresentationSpeakers speakers={moderators} />
					</div>
					}
					{
						  p.media_uploads_url &&
						  <div className="row">
							  <div className="col-sm-12">
								  <a href={p.media_uploads_url} target="_blank"><i className="fa fa-download" /> Materials</a>
							  </div>
						  </div>
					}
					<div className="row">
						<div className="col-sm-12">
							<a onClick={this.props.toggleEmailSpeakers}><i className="fa fa-envelope" /> Email the speakers</a>							
						</div>
					</div>
					{p.showForm &&
						<PresentationEmailForm />				
					}

					<PresentationActivity activity={p.comments} />
			      </div>
			   </div>
			</div>
        );
    }
}

export default connect(
	state => {
		const filteredPresentations = getFilteredPresentations(state);
		const myList = state.lists.results ? state.lists.results.find(l => l.mine) : null;
		let selectionsRemaining = myList ? (myList.slots - myList.selections.length) : null;
		const index = filteredPresentations.findIndex(p => p.id === state.detailPresentation.id)+1;

		return {
			presentation: state.detailPresentation,
			myList,
			selectionsRemaining,
			index,
			total: filteredPresentations.length,
			isAdmin: window.TrackChairAppConfig.userinfo.isAdmin
		}
	},
	dispatch => ({
		update(id) {
			dispatch(updateDetailPresentationID(id))
		},
		markAsRead(id) {
			dispatch(postMarkAsRead(id));
		},
		toggleEmailSpeakers(e) {
			e.preventDefault();
			dispatch(toggleEmailSpeakers());
		}

	})
)(BrowseDetail);