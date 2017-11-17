/*
collections.js

This file generates the collection view page. It makes several calls to the backend in order to fetch data from the database.
*/

//collectionsInit loaded when the page loads.
function collectionsInit(){
    getUser();
}

//displayCollections does all of the work of injecting HTML. Accepts the collections converted from a JSON object.
//Uses jQuery for HTML injection.
function displayCollections(collections){
    var html = $("<table>").addClass("table table-striped");
    if(collections != null){
        html.append(
            $('<thead>')).append(
                $('<tr>').append(
                    $('<th>').text('Name')
                ).append(
                    $('<th>').text('Description')
                ).append(
                    $('<th>').text('City')
                ).append(
                    $('<th>').text('State')
                ).append(
                    $('<th>').text('Status')
                ).append(
                    $('<th>')
                )
        );
        body = $('<tbody>');
        for(var i = 0; i < collections.length; i++){
            buttons = $('<div>');
            if(collections[i].Status == 1){
                buttons.append(
                    $('<button>', {
                        "class": "btn",
                        text: "Edit",
                        onClick: "location.href='/edit/" + collections[i].CID + "';"
                    }),
                    $('<button>', {
                        "class": "btn-danger",
                        text: "Disable",
                        onClick: "onClickDisable('" + collections[i].CID + "');"
                    })
                )
            } else {
                buttons.append(
                    $('<button>', {
                        "class": "btn",
                        text: "Edit",
                        onClick: "location.href='/edit?" + encodeURI(collections[i].CID) + "';"
                    }),
                    $('<button>', {
                        "class": "btn-success",
                        text: "Enable",
                        onClick: "onClickEnable('" + collections[i].CID + "');"
                    })
                )
            }
            body.append(
                $('<tr>').append(
                    $('<td>').text(collections[i].Name)
                ).append(
                    $('<td>').text(collections[i].Description)
                ).append(
                    $('<td>').text(collections[i].City)
                ).append(
                    $('<td>').text(collections[i].State)
                ).append(
                    $('<td>').text(collections[i].Status)
                ).append(buttons)
            );
        }

        html.append(body);
    } else {
        html = '<p>No collections yet.</p>'
    }

    $('#collections').html(html);
}

//Does the same as above, but changes the buttons for the admin.
function displayCollectionsAdmin(collections){
    var html = $("<table>").addClass("table table-striped");
    if(collections != null){
        html.append(
            $('<thead>')).append(
                $('<tr>').append(
                    $('<th>').text('Name')
                ).append(
                    $('<th>').text('Description')
                ).append(
                    $('<th>').text('City')
                ).append(
                    $('<th>').text('State')
                ).append(
                    $('<th>').text('Status')
                ).append(
                    $('<th>')
                )
        );
        body = $('<tbody>');
        for(var i = 0; i < collections.length; i++){
            buttons = $('<div>');
            if(collections[i].Status == 2){
                buttons.append(
                    $('<button>', {
                        "class": "btn",
                        text: "Edit",
                        onClick: "location.href='/edit/" + collections[i].CID + "';"
                    }),
                    $('<button>', {
                        "class": "btn-danger",
                        text: "Disapprove",
                        onClick: "onClickDisable('" + collections[i].CID + "');"
                    })
                )
            } else {
                buttons.append(
                    $('<button>', {
                        "class": "btn",
                        text: "Edit",
                        onClick: "location.href='/edit/" + encodeURI(collections[i].CID) + "';"
                    }),
                    $('<button>', {
                        "class": "btn-success",
                        text: "Approve",
                        onClick: "onClickApprove('" + collections[i].CID + "');"
                    })
                )
            }
            body.append(
                $('<tr>').append(
                    $('<td>').text(collections[i].Name)
                ).append(
                    $('<td>').text(collections[i].Description)
                ).append(
                    $('<td>').text(collections[i].City)
                ).append(
                    $('<td>').text(collections[i].State)
                ).append(
                    $('<td>').text(collections[i].Status)
                ).append(buttons)
            );
        }

        html.append(body);
    } else {
        html = '<p>No collections yet.</p>'
    }

    $('#collections').html(html);
}

//getUser gets the currently signed in user's ID from the database. Does so by sending an id_token to our /api/user/web endpoint.
function getUser(){
    var auth2 = gapi.auth2.getAuthInstance();
    var googleUser = auth2.currentUser.get();
    var id_token = googleUser.getAuthResponse().id_token;
    var obj = { 'id_token': id_token };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/api/user/web',
        data: json,
        success: function (ret) {
            if(ret != null){
                if(ret.UserID == 0) {
                    $('#h1col').text("ALL COLLECTIONS");
                }
                getCollections(ret.UserID);
            } else {
                //The user doesnt exist?!?!
            }
        },
        error: function (err) {
            console.log("Error getting user.");
        }
    });
}

//getCollections get's collections created by the user with userID.
function getCollections(userID){
    var obj = { 'UserID': userID };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/api/user/web/collections',
        data: json,
        success: function (ret) {
                if(userID == 0)
                    displayCollectionsAdmin(ret);
                else
                    displayCollections(ret);
        },
        error: function (err) {
            console.log("Error getting collections.");
        }
    });
}

//onClickDisable our callback function for the button to disable a collection.
function onClickDisable(cid){
    var obj = { 'cid': cid };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/api/collection/disable',
        data: json,
        success: function (ret) {
            collectionsInit();
        },
        error: function (err) {
            console.log("Error disabling collection.");
        }
    });
}

//onClickEnable our callback function for the button to enable a collection.
function onClickEnable(cid){
    var obj = { 'cid': cid };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/api/collection/enable',
        data: json,
        success: function (ret) {
            collectionsInit();
        },
        error: function (err) {
            console.log("Error enabling collection.");
        }
    });
}

//onClickApprove our callback function for the button to approve a collection.
function onClickApprove(cid){
    var obj = { 'cid': cid };
    var json = JSON.stringify(obj);

    //Verify token
    $.ajax({
        type: 'POST',
        url: '/api/collection/approve',
        data: json,
        success: function (ret) {
            collectionsInit();
        },
        error: function (err) {
            console.log("Error enabling collection.");
        }
    });
}