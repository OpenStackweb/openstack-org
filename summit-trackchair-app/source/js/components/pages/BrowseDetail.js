import React from 'react';
import {connect} from 'react-redux';
import Initials from '../ui/Initials';
import {fetchPresentationDetail} from '../../actions';
import PresentationCommentForm from '../containers/PresentationCommentForm';


class BrowseDetail extends React.Component {

	componentDidMount() {
		this.props.fetch(this.props.params.id);
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.params.id !== this.props.params.id) {
			this.props.fetch(nextProps.params.id);
		}
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
			               <div className="row">
			                  <div className="col-lg-3 pull-right">
			                     <a href="#" className="btn btn-primary btn-xs"><i className="fa fa-plus"></i> My list</a>
			                     <a href="#" className="btn btn-warning btn-xs"><i className="fa fa-plus"></i> Team list</a>
			                  </div>
			               </div>
			               <div className="m-b-md">
			                  <h2>{p.title}</h2>
			               </div>
			            </div>
			         </div>
			        <div className="row">
			            <div className="col-lg-5">
			               <dl className="dl-horizontal">
			                  <dt>Submitted by:</dt>
			                  <dd>{p.creator}</dd>
			               </dl>
			            </div>
			            <div className="col-lg-7" id="cluster_info">
			               <dl className="dl-horizontal">
			                  <dt>Level:</dt>
			                  <dd>{p.level}</dd>
			                  <dt>Category:</dt>
			                  <dd>{p.category_name}</dd>
			               </dl>
			            </div>
			        </div>
					<div className="row">
                    	<div className="col-lg-3">
	                        <div className="ibox">
	                            <div className="ibox-content">
	                                <h5>Total Selections</h5>
	                                <h1 className="no-margins">6</h1>
	                            </div>
	                        </div>
	                    </div>

                    	<div className="col-lg-3">
	                        <div className="ibox">
	                            <div className="ibox-content">
	                                <h5>Average Vote</h5>
	                                <h1 className="no-margins">{p.vote_average}</h1>
	                            </div>
	                        </div>
	                    </div>
                    	<div className="col-lg-3">
	                        <div className="ibox">
	                            <div className="ibox-content">
	                                <h5>Total Votes</h5>
	                                <h1 className="no-margins">{p.total_votes}</h1>
	                            </div>
	                        </div>
	                    </div>
                	</div>
                	<div className="row">
                		<div className="col-lg-12">
                			<div className="ibox">
                				<div className="ibox-content">
                					<h3>Description</h3>
                					<div dangerouslySetInnerHTML={{__html: p.description}} />
                				</div>
                			</div>
                		</div>
                	</div>		         
                	<div className="row">
                		<div className="col-lg-12">
                			<div className="ibox">
                				<div className="ibox-content">
                					<h3>Problems Addressed</h3>
                					<div dangerouslySetInnerHTML={{__html: p.problem_addressed}} />
                				</div>
                			</div>
                		</div>
                	</div>		         
                	<div className="row">
                		<div className="col-lg-12">
                			<div className="ibox">
                				<div className="ibox-content">
                					<h3>What Should Attendees Expect to Learn?</h3>
                					<div dangerouslySetInnerHTML={{__html: p.attendees_expected_learnt}} />
                				</div>
                			</div>
                		</div>
                	</div>		         

			         <div className="row m-t-sm">
			            <div className="col-lg-12">
			               <div className="panel blank-panel">
			                  <div className="panel-heading">
			                     <h3>Activity</h3>
			                  </div>
			                  <div className="panel-body">
			                     <div className="feed-activity-list">
			                        {p.comments.map(c => (
				                        <div key={c.id} className="feed-element">
				                        	<span className="pull-left">		                           
				                           		<Initials name={c.name} />
				                           	</span>
				                           <div className="media-body">
				                              <small className="pull-right">{c.ago}</small>
				                              <strong>{c.name}</strong> posted a comment<br />
				                              <small className="text-muted">{c.created}</small>
				                              <div className="well">
				                              	{c.body}
				                              </div>
				                           </div>
				                        </div>
			                        ))}
			                        <PresentationCommentForm />
			                     </div>
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
	state => ({
		presentation: state.detailPresentation,		
	}),
	dispatch => ({
		fetch(id) {
			dispatch(fetchPresentationDetail(id));
		}
	})
)(BrowseDetail);