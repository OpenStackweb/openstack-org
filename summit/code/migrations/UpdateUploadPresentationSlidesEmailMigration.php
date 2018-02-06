<?php
/**
 * Copyright 2018 OpenStack Foundation
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

final class UpdateUploadPresentationSlidesEmailMigration extends AbstractDBMigrationTask
{
    protected $title = "UpdateUploadPresentationSlidesEmailMigration";

    protected $description = "UpdateUploadPresentationSlidesEmailMigration";

    function doUp()
    {

        $sql = <<<SQL
UPDATE PermamailTemplate
SET Content='<html>
 <head>
    <meta charset="UTF-8">
</head> 
<body>

<p>{\$Speaker.FirstName},</p>
 
<p>We look forward to your presentation at the OpenStack Summit {\$Summit.Title}!</p>
<p>We would love for you to share any slides you have for the following presentations:</p>
<ul>
    <% loop \$Presentations %>
        <li><em>{\$Title}</em></li>
    <% end_loop %>
</ul>

<p>Please use the direct link below to upload your final presentation slides. Your slides will be made viewable on the OpenStack website alongside the video recording of your presentation, which you will be able to view on the website after {\$Summit.Title}.</p>

<p>Click this link to go to the upload area: <a href="{\$UploadSlidesURL}">{\$UploadSlidesURL}</a></p>

<p><i>Note: If there are multiple speakers in the presentation, you will be able to see if the slides have already been uploaded when you follow the link above.</i></p>

<p>Thank you for speaking at the OpenStack Summit and we will see you in {\$Summit.Title}!</p>
<p>OpenStack Speaker Support team</p>

</body>

</html>'
WHERE Identifier = 'upload-presentation-slides-email';
SQL;

        DB::query($sql);
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}