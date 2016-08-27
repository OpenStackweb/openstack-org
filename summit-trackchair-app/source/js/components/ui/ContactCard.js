import React from 'react';

export default ({
	imageURL,
	name,
	title,
	twitter,
	bio
}) => (
<div className="contact-box">    
    <div className="col-sm-4">
        <div className="text-center">
            <img alt="image" className="img-circle m-t-xs" src={imageURL} />
            <div className="m-t-xs font-bold">{title}</div>
	        <h3><strong>{name}</strong></h3>
	        {twitter &&
	        	<p><i className="fa fa-twitter"></i> {twitter}</p>
	    	}
        </div>
    </div>
    <div className="col-sm-8">
    	<div className="bio" dangerouslySetInnerHTML={{__html: bio}} />
    </div>
    <div className="clearfix"></div>        
</div>
);