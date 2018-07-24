import React from 'react';
import Config from '../../utils/Config';
export default ({
	loggedIn,
	votingOpen
}) => {
   	if(!loggedIn && votingOpen) {
   		return (
		  	<div>
		      <h5>Login to vote</h5>
			  <div className="login-to-vote">
			     <h3>Help this presentation get to the OpenStack Summit!</h3>
			     <p>We received hundreds of high-quality submissions and your votes can help determine which presentations are included in the Summit schedule.</p>
			     <a className="btn" href={`/Security/login?BackURL=${Config.get('baseURL')}`}>I already have an account</a>&nbsp; | &nbsp;
			     <a href="/summit-login/login" className="btn">Sign up now</a>
			  </div>
			</div>
   		);
   	}

   	else if(loggedIn && !votingOpen) {
   		return (
			<div className="alert alert-danger alert-dismissible" role="alert">
			    <button type="button" className="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			    Community Voting has closed. Thanks and we’ll see you at the next Summit.
			</div>
		);
   	}

   	return <div />;
}
