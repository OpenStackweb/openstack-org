<?php
/* What kind of environment is this: development, test, or live (ie, production)? */
define('SS_ENVIRONMENT_TYPE', '');
 
/* Database connection */
define('SS_DATABASE_SERVER', '');
define('SS_DATABASE_USERNAME', '');
define('SS_DATABASE_PASSWORD', '');
define('SS_DATABASE_CLASS','CustomMySQLDatabase');
//db name
$database = '';

/* Global variables */

$email_from = '';
$email_log = '';
$email_new_deployment = '';
/*
$mysqli = @new mysqli(SS_DATABASE_SERVER, SS_DATABASE_USERNAME, SS_DATABASE_PASSWORD, $database);
if ($mysqli->connect_errno) {
	mysqli_close($mysqli);
	ob_clean();
	$maintenance_page = file_get_contents(getcwd(). '/../maintenance/index.html');
	echo $maintenance_page;
	header("HTTP/1.0 502 Bad Gateway");
	exit();
}
mysqli_close($mysqli);
*/

//used by openstack/code/utils/email/DevelopmentEmail.php
define('DEV_EMAIL_TO','');

//used on events module.
define("EVENT_REGISTRATION_REQUEST_EMAIL_FROM", '');
// used on elections module.
define('REVOCATION_NOTIFICATION_EMAIL_FROM','');
//used on jobs module.
define('JOB_REGISTRATION_REQUEST_EMAIL_FROM','');
define('JOB_SUBMISSION_EMAIL_FROM','secretary@openstack.org');
//used on openstack/code/summit/TrackChairPage.php (EmailTrackChairs - ln 739)
define('TRACK_CHAIRS_EMAIL_FROM','');
//used on openstack/code/summit/SpeakerListPage.php (EmailSpeakers - ln 59)
define('SPEAKER_EMAIL_FROM','');
//used on openstack/code/summit/SchedToolsPage.php (EmailSpeakers - ln 59 )
define('SCHED_TOOLS_EMAIL_FROM','');
//used on openstack/code/summit/CallForSpeakersPage.php (EmailOnNewTalk - ln 89)
define('CALL_4_SPEAKERS_FROM_EMAIL','');
//used on marketplace reviews module.
define('MARKETPLACE_REVIEWS_EMAIL_TO','');
//used on marketplace admin.
define('MARKETPLACE_ADMIN_UPDATE_EMAIL_TO','');

define('DEPLOYMENT_SURVEY_THANK_U_FROM_EMAIL','');

define('SURVEY_COMPLETE_EMAIL_TO','');
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

// news module settings
define("NEWS_SUBMISSION_EMAIL_ALERT_TO", '');
define('NEWS_SUBMISSION_EMAIL_FROM','secretary@openstack.org');
define('NEWS_SUBMISSION_EMAIL_SUBJECT','New News item on Openstack.org');

//ICLA Module configuration
define('ICLA_GROUP_ID', '');
define('GERRIT_BASE_URL', '');
define('GERRIT_USER', '');
define('GERRIT_PASSWORD', '');
define('PULL_ICLA_DATA_FROM_GERRIT_BATCH_SIZE', 1000);
define('PULL_LAST_COMMITTED_DATA_FROM_GERRIT_BATCH_SIZE', 1000);
define('CCLA_TEAM_INVITATION_EMAIL_FROM','');
define('CCLA_DEBUG_EMAIL','');



//get a key from https://github.com/settings/applications#personal-access-tokens
define('GITHUB_API_OAUTH2TOKEN','');

define('APPSEC', ''); # openstack/code/MemberVerifyPage.php
// here u need to define your hostheader and your local path
// like $_FILE_TO_URL_MAPPING['/var/www/openstack.org'] = 'http://www..openstack.org';
// mainly this is used by the cron tasks

define('IDP_OPENSTACKID_URL','https://dev.openstackid.com');
//set true on production mode, otherwise false
define('Auth_OpenID_VERIFY_HOST',false);
define('Auth_OpenID_Realm','https://devbranch.openstack.org');
define('OPENSTACKID_ENABLED',true);

//summit purchase orders settings

define('APPROVED_PURCHASE_ORDER_EMAIL_FROM','');
define('APPROVED_PURCHASE_ORDER_EMAIL_SUBJECT','Your OpenStack Sponsorship Package Purchase Order was approved');
define('NEW_PURCHASE_ORDER_EMAIL_FROM','');
define('NEW_PURCHASE_ORDER_EMAIL_TO','');
define('NEW_PURCHASE_ORDER_EMAIL_SUBJECT','New Sponsorship Package Purchase Order');
define('REJECTED_PURCHASE_ORDER_EMAIL_FROM','');
define('REJECTED_PURCHASE_ORDER_EMAIL_SUBJECT','Your OpenStack Sponsorship Package Purchase Order was rejected');

// OAUTH 2.0 Client ID to use Google Calendar API
define('GAPI_CLIENT','');
define('GAPI_CLIENT_SECRET', '');
define('GAPI_REDIRECT_URL','http://local.openstack.org/summit-calendar-sync/login-google');
define('GAPI_SCOPES','email,profile,https://www.googleapis.com/auth/calendar');
define('GAPI_APPROVAL_PROMPT','force');
define('GAPI_ACCESS_TYPE','offline');

define('OUTLOOK_APP_PASSWORD','');
define('OUTLOOK_APP_ID','');
define('OUTLOOK_REDIRECT_URL','https://local.openstack.org/summit-calendar-sync/login-outlook');
define('OUTLOOK_SCOPES', 'openid profile offline_access User.Read https://outlook.office.com/calendars.readwrite');
// APP LINKS
// http://applinks.org/documentation/
//ios
define('APP_LINKS_IOS_APP_STORE_ID','');
define('APP_LINKS_IOS_APP_NAME','OpenStack Summit');
define('APP_LINKS_IOS_APP_CUSTOM_SCHEMA','org.openstack.ios.summit');
$APP_LINKS_IOS_FILE_CONFIG = [];
$APP_LINKS_IOS_FILE_CONFIG['org.openstack.ios.summit.debug'] = array('*');
$APP_LINKS_IOS_FILE_CONFIG['org.openstack.ios.summit.beta']  = array('*');
//android
define('APP_LINKS_ANDROID_PACKAGE','org.openstack.android.summit');
define('APP_LINKS_ANDROID_APP_NAME','OpenStackSummitApplication');
define('APP_LINKS_ANDROID_APP_CUSTOM_SCHEMA','org.openstack.android.summit');
$APP_LINKS_ANDROID_FILE_CONFIG = [];
$APP_LINKS_ANDROID_FILE_CONFIG[APP_LINKS_ANDROID_PACKAGE] = [
    "FA:39:81:5D:C8:A3:36:96:A1:4F:8B:21:DA:4B:9A:6B:71:36:9C:30:56:BB:C1:3F:40:BD:94:EC:B2:26:89:10",
    "3C:FA:62:3E:0B:C6:9F:81:EA:F1:74:4F:94:FC:55:6A:A4:28:0C:CB:11:4A:E3:34:30:D2:D7:6E:3D:A8:AA:8A",
];

define('GROUP_CONTACT_REPORT_TOKEN', '123456');
define('OPENSTACK_YOUTUBE_API_KEY','');
define('OPENSTACK_YOUTUBE_CHANNEL_ID','');

// Twitter
define('OPENSTACK_TWITTER_ACCOUNT', '');

define('FIREBASE_GCM_SERVER_KEY', '');
define('FIREBASE_API_KEY', '');
define('FIREBASE_PROJECT_ID', '');
define('FIREBASE_MESSAGING_SENDER_ID', '');

//auc-metrics/code/services/ActiveCommitterService.php
define('ACTIVECOMMITTERSERVICE_GERRIT_USER', '');
define('ACTIVECOMMITTERSERVICE_GERRIT_USER_SSH_KEY_FILE', '');
define('ACTIVECOMMITTERSERVICE_GERRIT_USER_SSH_KEY_FILE_PASSWORD', '');

define('SERVER_TIME_ZONE', 'America/Chicago');

define('RECAPTCHA_SITE_KEY', "");
define('RECAPTCHA_SECRET_KEY', "");
//marketplace/code/infrastructure/services/ExpiredPoweredOpenStackImplementationEmailMessageSender.php#32
define('EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL_TO', '');
// coa
define('SENDGRID_API_KEY', '');
define('COA_UNPROCESSED_EMAIL_ROWS_FROM', '');
define('COA_UNPROCESSED_EMAIL_ROWS_TO', '');
define('COA_UNPROCESSED_EMAIL_ROWS_SUBJECT', '');

// track chair tool
define('TRACK_CHAIR_TOOL_EMAIL_FROM', '');
/**
    use sake dev/tasks/GenerateEncryptionKeyTask to generate Encrypter_Key
 */
define('Encrypter_Key', '');
define('Encrypter_Cipher', 'AES-256-CBC');

//APPLE CALENDAR
define('CALDAV_BASE_SERVER_URL', 'https://p01-caldav.icloud.com:443');
//registration/code/model/member_spammer_estimator/MemberSpammerProcessorTask.php:49
define('MEMBER_SPAM_PROCESSOR_TO', '');

define('OPENSTACK_RESOURCE_API_BASE_URL', '');
define('SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_FROM', '');

define('SEARCH_WIDGET_BASE_URL', '');

global $_FILE_TO_URL_MAPPING;
$_FILE_TO_URL_MAPPING[''] = '';

