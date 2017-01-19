import React from 'react';
import MobileVotingBar from '../containers/MobileVotingBar';
import MobileMenuTrigger from '../containers/MobileMenuTrigger';
import MobileVoteTrigger from '../containers/MobileVoteTrigger';
import PresentationPagination from '../containers/PresentationPagination';

export default () => (
	<div className="mobile-tools">						
	   <MobileVotingBar />
	   <MobileMenuTrigger />
	   <MobileVoteTrigger />
	   <PresentationPagination />
	</div>
);
