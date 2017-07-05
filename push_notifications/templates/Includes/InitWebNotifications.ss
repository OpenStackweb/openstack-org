<script src="https://www.gstatic.com/firebasejs/4.1.3/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/3.9.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/3.9.0/firebase-messaging.js"></script>
<script>
    // API key
    var apiKey = "{$getEnv('FIREBASE_GCM_SERVER_KEY')}";
    var topic_channel = '{$topicChannel}';

    // Initialize Firebase
    var config = {
        apiKey: apiKey,
        authDomain: "os-local.firebaseapp.com",
        databaseURL: "https://os-local.firebaseio.com",
        projectId: "os-local",
        storageBucket: "",
        messagingSenderId: "71062358231"
    };
</script>

<script src="/push_notifications/javascript/push-notifications.js" type="application/javascript"></script>

