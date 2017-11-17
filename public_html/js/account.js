/*
account.js

This is the file that exports Google User data and displays it on the account page.
*/

//Called upon the page load
function accountInit(){
    getUser();
}

//Gets the currently signed in user.
function getUser(){
    var auth2 = gapi.auth2.getAuthInstance();
    var googleUser = auth2.currentUser.get();
    displayAccount(googleUser);
}

//Replaces the div tags with the appropriate data.
function displayAccount(googleUser){
    var basicProfile = googleUser.getBasicProfile();

    $('#email').html(basicProfile.getEmail());
    $('#name').html(basicProfile.getName());
}