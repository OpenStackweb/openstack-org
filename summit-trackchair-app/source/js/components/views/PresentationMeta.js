import React from 'react';
import RichTextSection from '../ui/RichTextSection';

export default ({
	presentation
}) => (
<div>
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
                  <dd>{presentation.category_name}</dd>
               </dl>
            </div>
        </div>			        
		<div className="row">
        	<div className="col-lg-4">
                <div className="ibox">
                    <div className="ibox-content">
                        <h5>Total Selections</h5>
                        <h1 className="no-margins">6</h1>
                    </div>
                </div>
            </div>

        	<div className="col-lg-4">
                <div className="ibox">
                    <div className="ibox-content">
                        <h5>Average Vote</h5>
                        <h1 className="no-margins">{presentation.vote_average}</h1>
                    </div>
                </div>
            </div>
        	<div className="col-lg-4">
                <div className="ibox">
                    <div className="ibox-content">
                        <h5>Total Votes</h5>
                        <h1 className="no-margins">{presentation.total_votes}</h1>
                    </div>
                </div>
            </div>
    	</div>
    	<RichTextSection title="Description" body={presentation.description} />
    	<RichTextSection title="Problems Addressed" body={presentation.problem_addressed} />
    	<RichTextSection title="What Should Attendees Expect to Learn?" body={presentation.attendees_expected_learnt} />
</div>

)