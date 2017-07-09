<script src="https://www.gstatic.com/firebasejs/4.1.3/firebase.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script>
    // API key
    var apiKey = "{$getEnv('FIREBASE_API_KEY')}";
    var serverKey = "{$getEnv('FIREBASE_GCM_SERVER_KEY')}";
    var topic_channel = '{$topicChannel}';
    var project_id = "{$getEnv('FIREBASE_PROJECT_ID')}";
    var sender_id = "{$getEnv('FIREBASE_MESSAGING_SENDER_ID')}";

    // Initialize Firebase
    var config = {
        apiKey: apiKey,
        authDomain: project_id+".firebaseapp.com",
        databaseURL: "https://"+project_id+".firebaseio.com",
        projectId: project_id,
        storageBucket: project_id+".appspot.com",
        messagingSenderId: sender_id
      };
</script>

<script src="/push_notifications/javascript/push-notifications.js" type="application/javascript"></script>

