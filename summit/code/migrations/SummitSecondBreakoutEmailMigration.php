<?php

/**
 * Copyright 2016 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
final class SummitSecondBreakoutEmailMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitSecondBreakoutEmailMigration";

    protected $description = "create the default email templates for summit second breakout";

    function doUp()
    {
        global $database;

        if(intval(PermamailTemplate::get()->filter('Identifier', PRESENTATION_SPEAKER_CONFIRM_SUMMIT_ASSISTANCE_EMAIL)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = PRESENTATION_SPEAKER_CONFIRM_SUMMIT_ASSISTANCE_EMAIL;
            $email_tpl->Subject    = 'Register for the OpenStack Summit Austin 2016';
            $email_tpl->From       = 'speakersupport@openstack.org';
$body =<<<HTML
<html>
<body>
<p>Hello \$Speaker.FirstName \$Speaker.LastName -- </p>
<p>We look forward to you joining us as a speaker for the OpenStack Summit in Austin! April 25th through April 29th.  Please respond to this email with your phone number where you can be reached while onsite.</p>
<p>This email includes important information, including your session details and presentation information so please review it carefully.  Going forward your main contacts (speaker managers) will be Beth Nowak (+1 415.994.8059) and Joe Schlick (+1 415.377.6370). They can be reached at speakersupport@openstack.org and will be able to help you with all of your conference needs. Beth and Joe will be on-site in Austin, Sunday, April 24th through Friday, April 29th to assist you.</p>
<p><b>REGISTRATION & SPEAKER CONFIRMATION</b></p>
<p>If you have not already completed both of these steps, please do so NOW!</p>
<p><b>STEP ONE: Register for the Summit.  Full Access FREE REGISTRATION CODE (IT'S UNIQUE & SINGLE-USE): {\$PromoCode}</b></p>
<p>In order to register for FREE you must use the code no later than April 19th, 2016.</p>
<p>
In Eventbrite there is a blue "Enter Promotional Code" option just above the “Order Now” button, where you may redeem the code for a free registration pass. Please reference this image for clarity:
<a href="https://www.eventbrite.com/e/openstack-summit-april-2016-austin-tickets-19805252042"><img src="https://dl.dropboxusercontent.com/s/m3jivcmfexctqeh/DiscountCodeImageAustin.jpg" alt="discount_code" title="discount code"/></a>
</p>
<p><b>STEP TWO: CONFIRM YOURSELF AS A SPEAKER</b></p>
<p>
Please click this link to confirm your attendance as a speaker: <a href="{\$ConfirmationLink}">\$ConfirmationLink</a>
</p>
<p><b>SESSION INFORMATION</b></p>
<p>Please review the Session Schedule for your Session Date, Time and Room Assignment:</p>
<ul>
<% loop \$Speaker.PublishedPresentations %>
<li><a href="{\$getLink}">\$Title ( \$getDateNice ) - \$getLocationNameNice </a></li>
<% end_loop %>
</ul>
<p><a href={\$ScheduleMainPageLink}">OpenStack Summit Schedule.</a></p>
<p>Also please take a moment to review your speaker information and verify that your name, title and Company are reflected accurately. If your headshot and/or biography do not appear on the schedule or the information is incorrect in any way, you can update your info by logging into your OpenStack Community Member account here: <a href="https://www.openstack.org/profile/speaker">https://www.openstack.org/profile/speaker</a>. We are also able to make the edits for you if needed.</p>
<p>Most sessions are 40 minutes long, with a 10-minute break between sessions. Please arrive to your session room at least 10 minutes prior to the start of your session and please be considerate of other speakers by ending your session on time.</p>
<p><b>PRESENTATION INFORMATION</b></p>
<p>We do not have a speaker ready room so please come prepared with your VGA or DVI compatible laptop with your 16:9 presentations loaded and ready to present. If your laptop needs an adapter to connect via VGA or DVI (i.e. Macs require a dongle), please bring that as well. If you require another type of connection, have a demo, or will use more than one connected device, please notify your speaker manager immediately.</p>
<p>If you have a presentation which requires attendees to download large files to participate in your session, please share your delivery system with the speaker managers so we are able to communicate that information with the attendees.  We suggest you provide a link to files several days prior to the conference.</p>
<p>We will be video recording sessions from a lockdown camera capturing the screen and presenter, and also a hard drive video recorder 1280x720 final resolution which will capture slides and camera in a two up format.  Recordings will be uploaded to OpenStack’s YouTube page and available on the OpenStack Foundation website post event. If you have not already done so, provide a 450 character abstract for YouTube by replying to this email.</p>
<p>Please include your name, title, company name, and title of your presentation in your introductory slide.</p>
<p><b>AV / ROOM SET UP</b></p>
<ul>
<li>**Sessions: small stage with presenter table (highboy), theater style seating for audience</li>
<li>**Workshops: small stage with presenter table (highboy), classroom style seating for audience</li>
<li>**Panels: chairs arranged in semi circle on stage with presenter table (highboy), theater style seating for audience</li>
</ul>
<p>A/V: each room is equipped with a projector (1280x720, 720p) and screen (16:9), DVI & VGA connections at 1920x1080 (capable of scaling if needed), 1 Seamless Switcher, 1 Wireless slide advancer, 3 wireless mics (handheld or lav), 1 Q&A mic set in aisle, 2 computer audio inputs on stage, Ethernet connections on stage, basic stage wash lighting and 1 standard power strip at the 30” cocktail round on stage. There will be an AV Tech in each room.</p>
<p>If you require additional equipment or need more information, please contact your speaker manager immediately.</p>
<p><b>PANEL INFORMATION</b></p>
<p>If you are moderating a panel and cannot meet with the panelists in person, please schedule a quick conference call with all participants to review expectations. A reminder for panels with 40 minutes: please introduce panelists first (we suggest no more than 30 sec. per person), follow with 15-20 minutes of prepared questions and discussions, and finish with 20 minutes of audience/interactive Q&A.</p>
<p>Thanks and see you soon!</p>
<p>OpenStack Summit Team</p>
</body>
</html>
HTML;



            $email_tpl->Content    = $body;
            $email_tpl->write();
        }

        if(intval(PermamailTemplate::get()->filter('Identifier', PRESENTATION_SPEAKER_SUMMIT_REMINDER_EMAIL)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = PRESENTATION_SPEAKER_SUMMIT_REMINDER_EMAIL;
            $email_tpl->Subject    = 'Thank you for registering or the OpenStack Summit';
            $email_tpl->From       = 'speakersupport@openstack.org';
            $body =<<<HTML
<html>
<body>
<p>Hello \$Speaker.FirstName \$Speaker.LastName -- </p>
<p>We look forward to you joining us as a speaker for the OpenStack Summit in Austin! April 25th through April 29th.  Please respond to this email with your phone number where you can be reached while onsite.</p>
<p>This email includes important information, including your session details and presentation information so please review it carefully.  Going forward your main contacts (speaker managers) will be Beth Nowak (+1 415.994.8059) and Joe Schlick (+1 415.377.6370). They can be reached at speakersupport@openstack.org and will be able to help you with all of your conference needs. Beth and Joe will be on-site in Austin, Sunday, April 24th through Friday, April 29th to assist you.</p>


<p><b>SESSION INFORMATION</b></p>
<p>Please review the Session Schedule for your Session Date, Time and Room Assignment:</p>
<ul>
<% loop \$Speaker.PublishedPresentations %>
<li><a href="{\$getLink}">\$Title ( \$getDateNice ) - \$getLocationNameNice </a></li>
<% end_loop %>
</ul>
<p><a href="{\$ScheduleMainPageLink}">OpenStack Summit Schedule.</a></p>
<p>Also please take a moment to review your speaker information and verify that your name, title and Company are reflected accurately. If your headshot and/or biography do not appear on the schedule or the information is incorrect in any way, you can update your info by logging into your OpenStack Community Member account here: <a href="https://www.openstack.org/profile/speaker">https://www.openstack.org/profile/speaker</a>. We are also able to make the edits for you if needed.</p>
<p>Most sessions are 40 minutes long, with a 10-minute break between sessions. Please arrive to your session room at least 10 minutes prior to the start of your session and please be considerate of other speakers by ending your session on time.</p>
<p><b>PRESENTATION INFORMATION</b></p>
<p>We do not have a speaker ready room so please come prepared with your VGA or DVI compatible laptop with your 16:9 presentations loaded and ready to present. If your laptop needs an adapter to connect via VGA or DVI (i.e. Macs require a dongle), please bring that as well. If you require another type of connection, have a demo, or will use more than one connected device, please notify your speaker manager immediately.</p>
<p>If you have a presentation which requires attendees to download large files to participate in your session, please share your delivery system with the speaker managers so we are able to communicate that information with the attendees.  We suggest you provide a link to files several days prior to the conference.</p>
<p>We will be video recording sessions from a lockdown camera capturing the screen and presenter, and also a hard drive video recorder 1280x720 final resolution which will capture slides and camera in a two up format.  Recordings will be uploaded to OpenStack’s YouTube page and available on the OpenStack Foundation website post event. If you have not already done so, provide a 450 character abstract for YouTube by replying to this email.</p>
<p>Please include your name, title, company name, and title of your presentation in your introductory slide.</p>
<p><b>AV / ROOM SET UP</b></p>
<ul>
<li>**Sessions: small stage with presenter table (highboy), theater style seating for audience</li>
<li>**Workshops: small stage with presenter table (highboy), classroom style seating for audience</li>
<li>**Panels: chairs arranged in semi circle on stage with presenter table (highboy), theater style seating for audience</li>
</ul>
<p>A/V: each room is equipped with a projector (1280x720, 720p) and screen (16:9), DVI & VGA connections at 1920x1080 (capable of scaling if needed), 1 Seamless Switcher, 1 Wireless slide advancer, 3 wireless mics (handheld or lav), 1 Q&A mic set in aisle, 2 computer audio inputs on stage, Ethernet connections on stage, basic stage wash lighting and 1 standard power strip at the 30” cocktail round on stage. There will be an AV Tech in each room.</p>
<p>If you require additional equipment or need more information, please contact your speaker manager immediately.</p>
<p><b>PANEL INFORMATION</b></p>
<p>If you are moderating a panel and cannot meet with the panelists in person, please schedule a quick conference call with all participants to review expectations. A reminder for panels with 40 minutes: please introduce panelists first (we suggest no more than 30 sec. per person), follow with 15-20 minutes of prepared questions and discussions, and finish with 20 minutes of audience/interactive Q&A.</p>
<p>Thanks and see you soon!</p>
<p>OpenStack Summit Team</p>
</body>
</html>
HTML;
            $email_tpl->Content = $body;
            $email_tpl->write();
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}