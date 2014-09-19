<?php

/**
 * Class TeamInvitationConfirmation_Controller
 */
final class TeamInvitationConfirmation_Controller extends AbstractController {
	/**
	 * @var array
	 */
	static $url_handlers = array(
		'GET $CONFIRMATION_TOKEN/confirm' => 'confirmTeamInvitation',
	);

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'confirmTeamInvitation',
	);

	/**
	 * @var ICLAMemberRepository
	 */
	private $member_repository;

	/**
	 * @var CCLATeamManager
	 */
	private $team_manager;

	/**
	 * @var ITeamInvitationRepository
	 */

	public function __construct(){
		parent::__construct();

		$this->member_repository     = new SapphireCLAMemberRepository;
		$this->invitation_repository = new SapphireTeamInvitationRepository;

		$this->team_manager          = new CCLATeamManager(
			$this->invitation_repository,
			$this->member_repository,
			new TeamInvitationFactory,
			new TeamFactory,
			new CCLAValidatorFactory,
			new SapphireTeamRepository,
			SapphireTransactionManager::getInstance());
	}

	/**
	 * @return string|void
	 */
	public function confirmTeamInvitation(){
		$token = $this->request->param('CONFIRMATION_TOKEN');
		try{
			$current_member = Member::currentUser();
			if(is_null($current_member))
				return Director::redirect("Security/login?BackURL=" . urlencode($_SERVER['REQUEST_URI']));
			$team = $this->team_manager->confirmInvitation($token, $current_member);
			return $this->renderWith( array('TeamInvitationConfirmation_successfull','Page') , array('TeamName' => $team->getName() , 'CompanyName' => $team->getCompany()->Name ) );
		}
		catch(InvitationBelongsToAnotherMemberException $ex1){
			SS_Log::log($ex1,SS_Log::ERR);
			$invitation = $this->invitation_repository->findByConfirmationToken($token);

			return $this->renderWith(array('TeamInvitationConfirmation_belongs_2_another_user','Page'), array('UserName' => $invitation->getMember()->Email));
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			return $this->renderWith(array('TeamInvitationConfirmation_error','Page'));
		}
	}
}