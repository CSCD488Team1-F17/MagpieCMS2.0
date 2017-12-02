function login() {
    // Initialize Firebase

    var config = {
        apiKey: "AIzaSyDoEaVyRVUmX7Ij-OxiiqVlUkaUYDPiMgo",
        authDomain: "magpie-3d047.firebaseapp.com",
        databaseURL: "https://magpie-3d047.firebaseio.com",
        projectId: "magpie-3d047",
        storageBucket: "magpie-3d047.appspot.com",
        messagingSenderId: "955947345618"
    };
    firebase.initializeApp(config);

    var provider = new firebase.auth.GoogleAuthProvider();

    firebase.auth().useDeviceLanguage();

    provider.setCustomParameters({
        'login_hint': 'user@example.com'
    });

    firebase.auth().signInWithPopup(provider).then(function (result) {
        // This gives you a Google Access Token. You can use it to access the Google API.
        var token = result.credential.accessToken;
        // The signed-in user info.
        var user = result.user;
        alert(user.valueOf());
        // ...
    }).catch(function (error) {
        // Handle Errors here.
        var errorCode = error.code;
        var errorMessage = error.message;
        // The email of the user's account used.
        var email = error.email;
        // The firebase.auth.AuthCredential type that was used.
        var credential = error.credential;
        // ...
    });
}
function onSignIn(googleUser) {
    console.log('Google Auth Response', googleUser);
    // We need to register an Observer on Firebase Auth to make sure auth is initialized.
    var unsubscribe = firebase.auth().onAuthStateChanged(function(firebaseUser) {
        unsubscribe();
        // Check if we are already signed-in Firebase with the correct user.
        if (!isUserEqual(googleUser, firebaseUser)) {
            // Build Firebase credential with the Google ID token.
            var credential = firebase.auth.GoogleAuthProvider.credential(
                googleUser.getAuthResponse().id_token);
            // Sign in with credential from the Google user.
            firebase.auth().signInWithCredential(credential).catch(function(error) {
                // Handle Errors here.
                var errorCode = error.code;
                var errorMessage = error.message;
                // The email of the user's account used.
                var email = error.email;
                // The firebase.auth.AuthCredential type that was used.
                var credential = error.credential;
                // ...
            });
        } else {
            console.log('User already signed-in Firebase.');
        }
    });
}
function isUserEqual(googleUser, firebaseUser) {
    if (firebaseUser) {
        var providerData = firebaseUser.providerData;
        for (var i = 0; i < providerData.length; i++) {
            if (providerData[i].providerId === firebase.auth.GoogleAuthProvider.PROVIDER_ID &&
                providerData[i].uid === googleUser.getBasicProfile().getId()) {
                // We don't need to reauth the Firebase connection.
                return true;
            }
        }
    }
    return false;
}
function signOut(){
    firebase.auth().signOut().then(function() {
        // Sign-out successful.
    }).catch(function(error) {
        // An error happened.
    });
}