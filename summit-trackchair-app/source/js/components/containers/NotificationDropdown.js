import React from 'react';
import {connect} from 'react-redux';
import ToggleDropdown from '../ui/ToggleDropdown';
import NotificationItem from '../ui/NotificationItem';

const NotificationDropdown = ({

}) => (
  	<ToggleDropdown component="LI" badge={4}>
  		<NotificationItem imageURL="summit-trackchair-app/source/images/a1.jpg" ago="3 hours ago" notes="Some notes">
  			<strong>Todd Morey</strong> moved the presentation <em>Just Do It in Perl</em> to <strong>Talks No One Will See</strong>
  		</NotificationItem>
  		<NotificationItem imageURL="summit-trackchair-app/source/images/a1.jpg" ago="3 hours ago" notes="Some notes">
  			<strong>Todd Morey</strong> moved the presentation <em>Just Do It in Perl</em> to <strong>Talks No One Will See</strong>
  		</NotificationItem>
  	</ToggleDropdown>
);

export default NotificationDropdown;