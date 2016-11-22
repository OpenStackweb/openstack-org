import React from 'react';

export default ({
	speakers
}) => (
 <div className="voting-speaker-row">
    <ul>
    	{speakers.map((speaker,i) => (
	       <li key={i}>
	          <img className="voting-speaker-pic" src={speaker.photoUrl} />
	          <div className="voting-speaker-name">
	             {speaker.first_name + ' ' + speaker.last_name}
	             <span>{speaker.title}</span>
	          </div>
	       </li>
    	))}
    </ul>
 </div>			         

);