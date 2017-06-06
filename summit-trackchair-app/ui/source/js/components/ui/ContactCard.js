import React from 'react';

export default ({
	imageURL,
	name,
	title,
	twitter,
	bio,
    available_for_bureau,
    profile_link,
    avg_rate_width,
    is_moderator
}) => (
<div className="contact-box">    
    <div className="col-sm-4">
        <div className="text-center">
            <a href={profile_link} target="_blank"> <img alt="image" className="img-circle m-t-xs" src={imageURL} /> </a>
            <div className="m-t-xs font-bold">{title}</div>
	        <h3><strong> <a href={profile_link} target="_blank">{name}</a> </strong></h3>
            {!is_moderator &&
            <div className="rating-container rating-gly-star" data-content="">
                <div className="rating-stars" data-content="" style={{width: avg_rate_width + '%'}}></div>
            </div>
            }
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