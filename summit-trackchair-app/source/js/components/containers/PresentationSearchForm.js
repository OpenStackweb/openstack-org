import React from 'react'

export default ({

}) => (
	<div className="input-group" style="padding:10px;background:#ddd;border-radius:5px;margin:10px 0;">
        <input type="text" placeholder="Search presentations " className="input form-control">
        <span className="input-group-btn">
                <button type="button" className="btn btn btn-primary"> <i className="fa fa-search"></i> Search</button>
        </span>
    </div>
);
