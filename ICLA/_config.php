<?php

//decorators
Object::add_extension('Member', 'ICLAMemberDecorator');
Object::add_extension('Company', 'ICLACompanyDecorator');
Object::add_extension('SangriaPage_Controller', 'SangriaPageICLACompaniesExtension');
Object::add_extension('EditProfilePage_Controller', 'EditProfilePageICLAExtension');

//configuration
define('ICLA_GROUP_ID', 'a49e4febb69477d0aa5737038c1802dd6cab67c5');
define('GERRIT_BASE_URL', 'https://review.openstack.org');
define('GERRIT_USER', 'smarcet');
define('GERRIT_PASSWORD', 'TwxKcgZurLX6');
define('PULL_ICLA_DATA_FROM_GERRIT_BATCH_SIZE', 1000);
define('PULL_LAST_COMMITTED_DATA_FROM_GERRIT_BATCH_SIZE', 1000);
define('CCLA_TEAM_INVITATION_EMAIL_FROM','noreply@openstack.org');

define('CCLA_DEBUG_EMAIL','smarcet@gmail.com');

PublisherSubscriberManager::getInstance()->subscribe('new_user_registered', function($member_id){
    //check if user has pending invitations
	$team_manager  = new CCLATeamManager(new SapphireTeamInvitationRepository,
		new SapphireCLAMemberRepository,
		new TeamInvitationFactory,
		new TeamFactory,
		new CCLAValidatorFactory,
		new SapphireTeamRepository,
		SapphireTransactionManager::getInstance());

	$team_manager->verifyInvitations($member_id, new TeamInvitationEmailSender(new SapphireTeamInvitationRepository));
});