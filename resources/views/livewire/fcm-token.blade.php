<div>

</div>
<script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js'
    import {getMessaging, onMessage, getToken} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyBHM0qQcP5p47u7eTKnKCnyzNP6d1eQslQ",
        authDomain: "x1io-309101.firebaseapp.com",
        databaseURL: "https://x1io-309101-default-rtdb.firebaseio.com",
        projectId: "x1io-309101",
        storageBucket: "x1io-309101.appspot.com",
        messagingSenderId: "926924779977",
        appId: "1:926924779977:web:8e96e6ac385207b7733818",
        measurementId: "G-LVRRBBLBJM"
    };
    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker
                    .register("/firebase-messaging-sw.js");
            }
            navigator.serviceWorker.getRegistration().then(async (reg) => {
                let token = await getToken(messaging, {vapidKey: "BPHPZpp9RcdvwFWmR_1AmfB7cosza4nI8UPDo1JNczUZTqmHczItpBixqx8grWv4VjUqvgY1QgA_De7ceHqVACQ"});
                if(token){
                    Livewire.dispatch('fcm-token', {token: token})
                }


                onMessage(messaging, (payload) => {
                    Livewire.dispatch('fcm-notification', {data: payload})
                    // push notification can send event.data.json() as well
                    const options = {
                        body: payload.data.body,
                        icon: payload.data.image,
                        tag: "alert",
                    };
                    let notification = reg.showNotification(
                        payload.data.title,
                        options
                    );
                    // link to page on clicking the notification
                    notification.onclick = (payload) => {
                        window.open(payload.data.url);
                    };
                });
            });
        }

    });

</script>
