<?php

//decorators
Object::add_extension('Member', 'ICLAMemberDecorator');
Object::add_extension('Company', 'ICLACompanyDecorator');
Object::add_extension('SangriaPage_Controller', 'SangriaPageICLACompaniesExtension');
Object::add_extension('EditProfilePage_Controller', 'EditProfilePageICLAExtension');

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