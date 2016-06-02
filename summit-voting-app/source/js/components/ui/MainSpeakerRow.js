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
		           <img className="voting-speaker-pic" src="" />
		        </div>
		        <div 
		        	className="main-speaker-description"
		        	dangerouslySetInnerHTML={{__html: speaker.bio}} />
	        </div>
     	))}
     </div>

);