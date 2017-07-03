<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Track Chairs App | OpenStack.org</title>
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:400,300,500,700">
        $ModuleCSS('main')

    </head>

    <body class="top-navigation">
        <div id="trackchair-app"></div>

        <script src="https://www.gstatic.com/firebasejs/4.1.3/firebase.js"></script>
        <script>
          // Initialize Firebase
          var config = {
            apiKey: "AIzaSyDydmxkjgnKem0az0dbombq0W6QFriPoI0",
            authDomain: "os-local.firebaseapp.com",
            databaseURL: "https://os-local.firebaseio.com",
            projectId: "os-local",
            storageBucket: "",
            messagingSenderId: "71062358231"
          };
          firebase.initializeApp(config);
        </script>
        <script type="text/javascript">
            window.TrackChairAppConfig = $JSONConfig;
        </script>
        $ModuleJS('main')
   </body>
</html>