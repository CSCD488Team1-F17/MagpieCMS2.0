/*
login.js

This file takes care of all of the login functionality on the website.
Unfortunately, much of this code is provided in different sections of Google's documentation without explanation.
It is highly suggested to research this topic at Google's website: https://developers.google.com/identity/sign-in/web/sign-in
*/

var GoogleAuth;
//Get base url
var pathArray = location.href.split('/');
var protocol = pathArray[0];
var host = pathArray[2];
var url = protocol + '//' + host;

function handleClientLoad() {
    // Load the API's client and auth2 modules.
    // Call the initClient function after the modules load.
    gapi.load('client:auth2', initClient);
}

function initClient() {
    gapi.load('auth2', function () {
        /**
         * Retrieve the singleton for the GoogleAuth library and set up the
         * client.
         */
        GoogleAuth = gapi.auth2.init({
            client_id: '1051549843498-vkaq31j25faui6j6tp6hccrulrklvmdt.apps.googleusercontent.com'
        });

        GoogleAuth.then(onInit, onError);
    });
}

function onInit() {
    if (GoogleAuth.isSignedIn.get()) {
        if(window.location.pathname === "/collections"){
            collectionsInit();
        } else if(window.location.pathname === "/account"){
            accountInit();
        }
    } else {
        window.location.href = url;
    }
}

function onError() {
    console.log("error");
}

function onSignIn(googleUser) {
    var id_token = googleUser.getAuthResponse().id_token;
    var obj = { 'id_token': id_token };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/tokensignin',
        data: json,
        success: function () {
            window.location.href = url + "/collections";
        },
        error: function (err) {
            console.log("Error logging in.");
        }
    });
}

function onSignOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
        auth2.disconnect();
        window.location.href = url;
    });
}