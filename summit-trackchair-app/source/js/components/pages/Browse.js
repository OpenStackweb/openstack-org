import React from 'react';
import PresentationList from '../views/PresentationList';
import {connect} from 'react-redux';
import {fetchPresentations} from '../../actions';
import CategoryDropdown from '../containers/CategoryDropdown';
import PresentationSearchForm from '../containers/PresentationSearchForm';

class Browse extends React.Component {

	constructor (props) {
		super(props);
		this.requestMore = this.requestMore.bind(this);
	}

	componentDidMount() {
		this.props.fetch();
	}

	requestMore() {
		this.props.fetch(this.props.currentPage+1);
	}

    render () {
    	if(!this.props.presentations) {
    		return <div>loading</div>
    	}

        return (
            <div>
			               <div className="col-lg-4">
			                  <div className="ibox float-e-margins">
			                  	<PresentationSearchForm />
			                     <div className="ibox-content">
			                        <div className="feed-activity-list">
			                           <div className="feed-element active">
			                              <div>
			                                 <strong>Developing, Deploying, and Consuming L4-7 Network Services in an OpenStack Cloud</strong>
			                                 <div>Jogn Angel, Jessica Ocean</div>
			                                 <small className="pull-right text-navy">Avg Vote: 2.6</small>
			                              </div>
			                           </div>
			                           <div className="feed-element">
			                              <div>
			                                 <strong>Developing, Deploying, and Consuming L4-7 Network Services in an OpenStack Cloud</strong>
			                                 <div>Jogn Angel, Jessica Ocean</div>
			                                 <small className="pull-right text-navy">Avg Vote: 2.6</small>
			                              </div>
			                           </div>
			                           <div className="feed-element">
			                              <div>
			                                 <strong>Developing, Deploying, and Consuming L4-7 Network Services in an OpenStack Cloud</strong>
			                                 <div>Jogn Angel, Jessica Ocean</div>
			                                 <small className="pull-right text-navy">Avg Vote: 2.6</small>
			                              </div>
			                           </div>
			                           <div className="feed-element">
			                              <div>
			                                 <strong>Developing, Deploying, and Consuming L4-7 Network Services in an OpenStack Cloud</strong>
			                                 <div>Jogn Angel, Jessica Ocean</div>
			                                 <small className="pull-right text-navy">Avg Vote: 2.6</small>
			                              </div>
			                           </div>
			                        </div>
			                     </div>
			                  </div>
			               </div>
			               <div className="col-lg-8">
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
			                                    <h2>Developing, Deploying, and Consuming L4-7 Network Services in an OpenStack Cloud</h2>
			                                 </div>
			                                 <dl className="dl-horizontal">
			                                    <dt>Status:</dt>
			                                    <dd><span className="label label-primary">Received</span></dd>
			                                 </dl>
			                              </div>
			                           </div>
			                           <div className="row">
			                              <div className="col-lg-5">
			                                 <dl className="dl-horizontal">
			                                    <dt>Created by:</dt>
			                                    <dd>Alex Smith</dd>
			                                    <dt>Average vote:</dt>
			                                    <dd>  2.6</dd>
			                                    <dt>Total votes:</dt>
			                                    <dd>  30</dd>
			                                 </dl>
			                              </div>
			                              <div className="col-lg-7" id="cluster_info">
			                                 <dl className="dl-horizontal">
			                                    <dt>Level:</dt>
			                                    <dd>Advanced</dd>
			                                    <dt>Category:</dt>
			                                    <dd>Enterprise IT Strategies</dd>
			                                    <dt>Presenters:</dt>
			                                    <dd className="project-people">
			                                       <a href=""><img alt="image" className="img-circle" src="img/a3.jpg"></a>
			                                       <a href=""><img alt="image" className="img-circle" src="img/a1.jpg"></a>
			                                       <a href=""><img alt="image" className="img-circle" src="img/a2.jpg"></a>
			                                       <a href=""><img alt="image" className="img-circle" src="img/a4.jpg"></a>
			                                       <a href=""><img alt="image" className="img-circle" src="img/a5.jpg"></a>
			                                    </dd>
			                                 </dl>
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
			                                          <div className="feed-element">
			                                             <a href="#" className="pull-left">
			                                             <img alt="image" className="img-circle" src="img/a2.jpg">
			                                             </a>
			                                             <div className="media-body ">
			                                                <small className="pull-right">2h ago</small>
			                                                <strong>Mark Johnson</strong> posted a comment<br>
			                                                <small className="text-muted">Today 2:10 pm - 12.06.2014</small>
			                                                <div className="well">
			                                                   Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
			                                                   Over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
			                                                </div>
			                                             </div>
			                                          </div>
			                                          <div className="feed-element">
			                                             <a href="#" className="pull-left">
			                                             <img alt="image" className="img-circle" src="img/a3.jpg">
			                                             </a>
			                                             <div className="media-body ">
			                                                <small className="pull-right">2h ago</small>
			                                                <strong>Janet Rosowski</strong> changed the category to <strong>Enterprise IT Strategies</strong>. <br>
			                                                <small className="text-muted">2 days ago at 8:30am</small>
			                                             </div>
			                                          </div>
			                                          <div className="chat-form">
			                            <form role="form">
			                                <div className="form-group">
			                                    <textarea className="form-control" placeholder="Write a comment..."></textarea>
			                                </div>
			                                <div className="text-right">
			                                    <button type="submit" className="btn btn-sm btn-primary m-t-n-xs"><strong>Post comment</strong></button>
			                                </div>
			                            </form>
			                        </div>
			                                       </div>
			                                    </div>
			                                 </div>
			                              </div>
			                           </div>
			                        </div>
			                     </div>
			                  </div>
			               </div>

                <h3>browse</h3>
                <div style={{float: 'left', width: '250px'}}>
                <CategoryDropdown />
                <PresentationList 
                	presentations={this.props.presentations} 
                	hasMore={this.props.hasMore}
                	onRequestMore={this.requestMore}
                	/>
                </div>
                <div style={{float: 'left', marginLeft: '250px'}}>
                	{this.props.children}
                </div>
            </div>
        );
    }
}
export default connect(
	state => ({
		presentations: state.presentations.results,
		hasMore: state.presentations.has_more,
		currentPage: state.presentations.page
	}),
	dispatch => ({
		fetch(page = 1) {			
			dispatch(fetchPresentations({page}))
		}
	})
)(Browse);