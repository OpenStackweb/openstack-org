<!DOCTYPE html>
<html>

    <head>
        <% base_tag %>
        <title>$PageTitle</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:400,300,500,700">
        $ModuleCSS('main')

    </head>

    <body class="top-navigation">
        <div id="trackchair-app"></div>

        <% include InitWebNotifications topicChannel='trackchairs' %>

        <script type="text/javascript">
            window.TrackChairAppConfig = $JSONConfig;
        </script>
        $ModuleJS('main')
        <% include TwitterUniversalWebsiteTagCode %>
        <% include GoogleAdWordsSnippet %>
   </body>
</html>