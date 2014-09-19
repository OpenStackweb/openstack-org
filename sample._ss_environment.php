<?php
/* What kind of environment is this: development, test, or live (ie, production)? */
define('SS_ENVIRONMENT_TYPE', '');
 
/* Database connection */
define('SS_DATABASE_SERVER', '');
define('SS_DATABASE_USERNAME', '');
define('SS_DATABASE_PASSWORD', '');
//db name
$database = '';

/* Global variables */

$email_from = '';
$email_log = '';
$email_new_deployment = '';

// DB connection checking code
$conn = mysql_connect(SS_DATABASE_SERVER, SS_DATABASE_USERNAME, SS_DATABASE_PASSWORD, true);

if(!$conn) {
	ob_end_clean();
	header("HTTP/1.0 500 Server Error DB");
	exit();
}
if(!mysql_select_db($database, $conn)){
	ob_end_clean();
	mysql_close($conn);
	header("HTTP/1.0 500 Server Error DB");
	exit();
}
mysql_close($conn);

//used by openstack/code/utils/email/DevelopmentEmail.php
define('DEV_EMAIL_TO','');
//used on events module.
define("EVENT_REGISTRATION_REQUEST_EMAIL_FROM", '');
// used on elections module.
define('REVOCATION_NOTIFICATION_EMAIL_FROM','');
//used on jobs module.
define('JOB_REGISTRATION_REQUEST_EMAIL_FROM','');
//used on openstack/code/UserStoriesHolder.php (submitNewUserStory - ln 102)
define('USER_STORIES_NEW_SUBMISSION_EMAIL_FROM','');
//used on openstack/code/UserStoriesHolder.php (submitNewUserStory - ln 102)
define('USER_STORIES_NEW_SUBMISSION_EMAIL_TO','');
//used on openstack/code/summit/TrackChairPage.php (EmailTrackChairs - ln 739)
define('TRACK_CHAIRS_EMAIL_FROM','');
//used on openstack/code/summit/SpeakerListPage.php (EmailSpeakers - ln 59)
define('SPEAKER_EMAIL_FROM','');
//used on openstack/code/summit/SchedToolsPage.php (EmailSpeakers - ln 59 )
define('SCHED_TOOLS_EMAIL_FROM','');
//used on openstack/code/summit/CallForSpeakersPage.php (EmailOnNewTalk - ln 89)
define('CALL_4_SPEAKERS_FROM_EMAIL','');

define('DEPLOYMENT_SURVEY_THANK_U_FROM_EMAIL','');
//used on openstack/code/FeedbackForm.php (submitFeedback - ln 46)
define('FEEDBACK_FORM_FROM_EMAIL','');
define('FEEDBACK_FORM_TO_EMAIL','');
//used on openstack/code/OSLogoProgramForm.php (save- ln 133)
define('OS_LOGO_PROGRAM_FORM_FROM_EMAIL','');
define('OS_LOGO_PROGRAM_FORM_TO_EMAIL','');
//used on openstack/code/summit/PresentationVotingPage.php (Done - ln 257)
define('PRESENTATION_VOTING_EVENT_FROM_EMAIL','');
define('PRESENTATION_VOTING_EVENT_TO_EMAIL','');
//used on openstack/code/MemberListPage.php (saveNomination - ln 274)
define('CANDIDATE_NOMINATION_FROM_EMAIL','');

//used on openstack/code/summit/PresentationEditorPage.php (EmailSubmitters - ln 534)
define('OS_SUMMIT_PRESENTATION_VOTING_FROM_EMAIL','');
//used on openstack/code/summit/PresentationEditorPage.php (EmailSpeakers - ln 573)
define('OS_SUMMIT_SPEAKING_SUBMISSION_FROM_EMAIL','');
//used on openstack/code/summit/PresentationEditorPage.php (AssembleEmail - ln 1138)
define('OS_PRESENTATION_SUBMISSIONS_FROM_EMAIL','');
// here u need to define your hostheader and your local path
// like $_FILE_TO_URL_MAPPING['/var/www/openstack.org'] = 'http://www..openstack.org';
// mainly this is used by the cron tasks
global $_FILE_TO_URL_MAPPING;
$_FILE_TO_URL_MAPPING[''] = '';
