import React from 'react';
import RichTextSection from '../ui/RichTextSection';
import Tooltip from '../ui/Tooltip';
import {Maybe, Selected, Help, Score} from '../ui/Icons';
import CategorySelector from '../containers/CategorySelector';
import {postCategoryChange} from '../../actions';
import {connect} from 'react-redux';
import SlideToggle from '../ui/animate/SlideToggle';

class PresentationMeta extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			showCategoryChange: false,
			selectedCategory: +props.presentation.category_id
		};
		this.toggleCategoryChange = this.toggleCategoryChange.bind(this);
		this.selectCategory = this.selectCategory.bind(this);
		this.onSubmit = this.onSubmit.bind(this);	
	}

	toggleCategoryChange(e) {
		e.preventDefault();
		this.setState({
			showCategoryChange: !this.state.showCategoryChange
		});
	}

	selectCategory(id) {
		this.setState({
			selectedCategory: id
		});
	}

	onSubmit(e) {
		e.preventDefault();
		if(this.state.selectedCategory == this.props.presentation.category_id) {
			return;
		}
		
		this.props.handleFormSubmit(
			this.props.presentation.id,
			this.state.selectedCategory
		);
	}

	render() {
		const {presentation, requesting, success} = this.props;

		const tooltip = (
			<Tooltip
				key='popularity'
				arrowContent={<div className="rc-tooltip-arrow-inner"></div>}
				placement="left" 
				overlay="Popularity score is a composite rating<br>that weights individual selections, <br>maybes, and passes from everyone on the team"
			>
				<i className="fa fa-question-circle" />
			</Tooltip>
		);
		return (
		<div className="presntation-meta">
		        <div className="row">
		            <div className="col-lg-5">
		               <dl className="dl-horizontal">
		                  <dt>Submitted by:</dt>
		                  <dd>{presentation.creator}</dd>
		               </dl>
		            </div>
		            <div className="col-lg-7" id="cluster_info">
		               <dl className="dl-horizontal">
		                  <dt>Level:</dt>
		                  <dd>{presentation.level}</dd>
		                  <dt>Category:</dt>
		                  <dd>
		                  	{presentation.category_name}<br />
		                  		(<a href="#" onClick={this.toggleCategoryChange}>Request category change</a>)

		                  </dd>                  
		               </dl>		               
		            </div>
		        </div>
              	{this.state.showCategoryChange &&
              	<div className="row">
              		<div className="col-lg-8 col-lg-offset-2">
	              		<div className="change-request-form">
							<form onSubmit={this.onSubmit}>
								{!success &&
								<label>In which category would you rather see this presentation?</label>
								}
								{success &&
									<div className="alert alert-success">
										Nice one! Your request has been submitted.
										(<a href="#" onClick={this.toggleCategoryChange}>close</a>)
									</div>
								}
								<div className="input-group presentation-search">

									<CategorySelector onSelect={this.selectCategory} activeCategory={this.state.selectedCategory} />
									<span className="input-group-btn">
										{requesting &&
										<button type="button" disabled className="btn btn btn-primary">
											<i className="fa fa-check" /> Requesting...
										</button>
										}
										{!requesting &&
										<button type="submit" className="btn btn btn-primary">
											<i className="fa fa-check" /> Submit request
										</button>
										}

										<button onClick={this.toggleCategoryChange} type="button" className="btn btn-default">
											<i className="fa fa-ban" /> Cancel
										</button>
									</span>
								</div>
							</form>						
	              		</div>
              		</div>
              	</div>             		              	
              	}
				<div className="row presentation-data">
		        	<div className="col-lg-3 presentation-data-vote">
		                <div className="ibox">
		                    <div className="ibox-content">
		                        <h5>Community Vote</h5>
		                        <h1 className="no-margins">{presentation.vote_average.toFixed(2)}</h1>
		                        <small>Average of <strong>{presentation.total_votes}</strong> total</small>
		                    </div>
		                </div>
		            </div>
		        	<div className="col-lg-3 presentation-data-selections">
		                <div className="ibox">
		                    <div className="ibox-content">
		                        <h5>Selections</h5>
		                        <h1 className="no-margins">{presentation.selectors.length}</h1>
		                        <small><Selected /></small>
		                    </div>
		                </div>
		            </div>
		        	<div className="col-lg-3 presentation-data-interest">
		                <div className="ibox">
		                    <div className="ibox-content">
		                        <h5>Interested</h5>
		                        <h1 className="no-margins">{presentation.likers.length}</h1>
		                        <small><Maybe /></small>
		                    </div>
		                </div>
		            </div>

		        	<div className="col-lg-3 presentation-data-popularity">
		                <div className="ibox">				
		                    <div className="ibox-content">
		                        <h5>Popularity Score {tooltip}</h5>
		                        <h1 className="no-margins">{presentation.popularity}</h1>
		                        <small><Score /></small>
		                    </div>
		                </div>
		            </div>
		    	</div>
		    	<RichTextSection title="Description" body={presentation.description} />
		    	<RichTextSection title="Problems Addressed" body={presentation.problem_addressed} />
		    	<RichTextSection title="What Should Attendees Expect to Learn?" body={presentation.attendees_expected_learnt} />
		</div>
		);		
	}	
}
export default connect(
	state => ({
		showChangeRequest: state.detailPresentation.showChangeRequest,
		requesting: state.detailPresentation.requesting,
		success: state.detailPresentation.categorySuccess
	}),
	dispatch => ({
		handleFormSubmit(presentationID, categoryID) {
			dispatch(postCategoryChange(presentationID, categoryID));
		}
	})
)(PresentationMeta);