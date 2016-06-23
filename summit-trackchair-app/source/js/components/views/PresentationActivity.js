import React from 'react';
import PresentationCommentForm from '../containers/PresentationCommentForm';
import Initials from '../ui/Initials';

export default ({
	activity
}) => (
 <div className="row m-t-sm">
    <div className="col-lg-12">
       <div className="panel blank-panel">
          <div className="panel-heading">
             <h3>Activity</h3>
          </div>
          <div className="panel-body">
             <div className="feed-activity-list">
                {activity.map(c => (
                    <div key={c.id} className={`feed-element ${c.is_activity ? 'activity' : 'comment'}`}>
                    	<span className="pull-left">
                    	{c.is_activity &&
							<span className="initials activity"><i className="fa fa-exclamation" /></span>
                    	}                           
                       	{!c.is_activity &&
							<Initials name={c.name} />
                       	}
                       	</span>
                       <div className="media-body">
                          <small className="pull-right">{c.ago}</small>
                          {!c.is_activity &&
							<span><strong>{c.name}</strong> posted a comment</span>
                          }                          
                          <br />
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

)