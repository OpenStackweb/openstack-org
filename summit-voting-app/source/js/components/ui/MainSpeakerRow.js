import React from 'react';

export default ({
	speakers
}) => (
     <div className="main-speaker-wrapper">
     	{speakers && speakers.map((speaker, i)  => (
	        <div key={i}>
		        <div className="main-speaker-row">
		           <div className="voting-speaker-name">
		              {speaker.first_name + ' ' + speaker.last_name}
		              <span>{speaker.title}</span>
		           </div>
		           
		        </div>
		        <div className="row">
		        	<div className="col-xs-12 col-lg-3">
		        		<img className="voting-speaker-pic" src={speaker.photoUrl} />
		        	</div>
		        	<div className="col-xs-12 col-lg-9">
				        <div 
				        	className="main-speaker-description"
				        	dangerouslySetInnerHTML={{__html: speaker.bio}} />
		        	</div>
		        </div>
	        </div>
     	))}
     </div>

);