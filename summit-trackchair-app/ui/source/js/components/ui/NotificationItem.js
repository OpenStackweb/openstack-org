import React from 'react';

export default ({
	imageURL,
	ago,
	children,
	notes
}) => (
   
      <div className="dropdown-messages-box">
         <span className="pull-left">
         	<img alt="image" className="img-circle" src={imageURL} />
         </span>
         <div className="media-body">
            <small className="pull-right">{ago}</small>
            {children} <br />
            <small className="text-muted">{notes}</small>
         </div>
      </div>
);