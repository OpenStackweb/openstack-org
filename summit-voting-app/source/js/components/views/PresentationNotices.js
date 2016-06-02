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
			     <p>OpenStack community members are voting on presentations to be presented at the OpenStack Summit in Tokyo, Japan. We received hundreds of high-quality submissions, and your votes can help us determine which ones to include in the schedule.</p>
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
			    Community Voting is closed. Thanks and we’ll see you next Summit.
			</div>
		);
   	}

   	return <div />;
}