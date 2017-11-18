<?php

/*
 * Currently just being used for reference
 *
 *
 */



/*
webapp.php

This file contains all of our routes and their helper functions for the web application.

// Suggestions //

Lot's of code was repeated towards the end to meet the time limit.
A lot of it can be removed and put into other functions for better reuse.

When a user attempts to edit a collection they did not make it simply yells at them "hey!". A better notice should be given.

The function canEdit can be improved.
*/





    //Enforce type restriction. Not necessary and wasn't strongly used.
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    //// Helper Functions ////

    //authCheck is a function that ensures that a given user is logged in before returning their requested page.
    //This is our primary form of security for the web application.
    function authCheck($path, $app, Request $request, Response $response, $args)
    {
        //A session must be started in order for Google' oauth2callback to work
        session_start();

        //Get our config object and fetch our firebase_credentials.json file
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);

        //If we have an access_token then we can render the page. Else we redirect to our oauth2callback endpoint.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            return $app->view->render($response, $path, $args);
        } else {
            return $response->withRedirect('/oauth2callback');
        }
    }

    //canEdit is a function that ensures that when a user attempts to edit a collection they can only edit their own.
    //This function needs improvement, it basically acts as a second authCheck with some questionable methods.
    //Their are no known bugs however.
    function canEdit(Response $response, $cid)
    {
        //A session must be started in order for Google' oauth2callback to work
        session_start();

        //Get our config object and fetch our firebase_credentials.json file
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);

        //If we have an access_token then we can fetch our user, else we must fetch one.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            $token = $client->getAccessToken();
            $client->setAccessToken($token);

            //Verify token with Google. If we fetch data then we can continue, otherwise something failed.
            if ($token_data = $client->verifyIdToken()) {
                //Token data contains our unique userID in the array field "sub"
                $userid = $token_data['sub'];

                $conn = connect_db();

                $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
                $stmt->execute([$userid]);
                $output = $stmt->fetch();

                //Check if our user exists
                if ($output['UID'] == $userid) {
                    //Check if our userID matches the UserID associated with the provided CID. If so, they own it and can edit it.
                    $stmt = $conn->prepare("SELECT * FROM UserMadeCollectionList WHERE (UserID = ? AND CollectionID = ?);");
                    $stmt->execute([$output['UserID'], $cid]);
                    if ($stmt->fetch()) {
                        return true;
                    } 
                    // UserID 0 is a special ID that belongs to the admin. If the UserID is 0 then they can edit the collection.
                    else if($output['UserID'] == 0){
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            if ($response->withRedirect('/oauth2callback')) {
                //Get access token
                $client->setAccessToken($_SESSION['access_token']);
                $token = $client->getAccessToken();
                $client->setAccessToken($token);

                if ($token_data = $client->verifyIdToken()) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    //// Routes ////

    //This is our main page that is loaded at magpiehunt.com. It displays some information and ask the user to login/create an account.
    $app->get('/', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'index.twig', $args);
    });

    //login is our login page
    $app->get('/login', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'login.twig', $args);
    });

    //oauth2callback is a function utilized by Google in order to authorize a Google sign-in.
    $app->get('/oauth2callback', function (Request $request, $response, $args) {
        //Start a session, without it it won't work.
        session_start();

        //Load our config file.
        $config = require dirname(__FILE__, 2) . '/config.php';

        //Create our Google Client, load the credentials, set our redirect uri, and add our scope (openid means just give us unique token data).
        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        //Get our parameters
        $allGetVars = $request->getQueryParams();

        //If we have a code then we can login, else we need to redirect back with the code
        if (!array_key_exists('code', $allGetVars)) {
            $auth_url = $client->createAuthUrl();
            return $response->withRedirect($auth_url, 200);
        } else {
            $getCode = $allGetVars['code'];
            $client->authenticate($getCode);
            $_SESSION['access_token'] = $client->getAccessToken();
            return $response->withRedirect('/');
        }
    });

    //tokensignin is our function for logging in users or adding user to the database
    $app->post('/tokensignin', function (Request $request, $response, $args) {
        //As usual, get our config file and create our Google Client
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        //Here we get the body of the request in order to fetch the id_token
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];

        //Next we verify the id_token provided and get the payload. If the payload is empty we return an error status.
        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            //The payload is good and we get the true Google ID
            $userid = $payload['sub'];

            //Next we connect to the database and see if the user exists. If not we add them.
            $conn = connect_db();

            $stmt = $conn->prepare("SELECT UID FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();
            if ($output['UID'] != $userid) {
                $stmt = $conn->prepare("INSERT INTO WebUserData (UID) VALUES (?)");
                $stmt->execute([$userid]);
            }

            $conn = null;
            return $response->withStatus(200);
        } else {
            return $response->withStatus(300);
        }
    });

    $app->get('/collections', function (Request $request, Response $response, $args) {
        return authCheck('collections.twig', $this, $request, $response, $args);
    });

    $app->get('/create', function (Request $request, Response $response, $args) {
        $cid;
        $conn = connect_db();
        $output = $conn->query("SELECT MAX(CID) AS MaxCid FROM Collections;");
        while ($row = $output->fetch()) {
            $cid = $row['MaxCid'] + 1;
        }
        $conn = null;

        return authCheck('create.twig', $this, $request, $response, ['cid'=> $cid]);
    });

    $app->get('/contact', function (Request $request, Response $response, $args) {
        return authCheck('contact.twig', $this, $request, $response, $args);
    });

    $app->get('/legal', function (Request $request, Response $response, $args) {
        return authCheck('legal.twig', $this, $request, $response, $args);
    });

    $app->get('/account', function (Request $request, Response $response, $args) {
        return authCheck('account.twig', $this, $request, $response, $args);
    });

    $app->get('/landmarks/{cid}', function (Request $req, Response $response, $args) {
        $numBadge;
        $ara = array();
        $araDesc = array();
        $cid = (int)$req->getAttribute('cid');
        $conn = connect_db();
        $stmt = $conn->prepare("SELECT NumberOfLandmarks FROM Collections WHERE CID = ?;");
        $stmt->execute([$cid]);
        $output = $stmt->fetch();
        $numBadge = $output['NumberOfLandmarks'];
        $stmt = $conn->prepare("SELECT Count(LID) AS count FROM Landmarks INNER JOIN CollectionLandmarks ON CollectionLandmarks.LandmarkID = Landmarks.LID WHERE CollectionLandmarks.CollectionID = ?;");
        $stmt->execute([$cid]);
        $output = $stmt->fetch();
        $landmarkCount = $output['count'];
        if ($landmarkCount > 0) {
            $stmt = $conn->prepare("SELECT * FROM Landmarks INNER JOIN CollectionLandmarks ON CollectionLandmarks.LandmarkID = Landmarks.LID WHERE CollectionLandmarks.CollectionID = ?;");
            $stmt->execute([$cid]);
            while ($row = $stmt->fetch()) {
                array_push($ara, $row);
            }

            $stmt = $conn->prepare("SELECT * FROM LandmarkDescription INNER JOIN Landmarks ON Landmarks.LID = LandmarkDescription.LID WHERE LandmarkDescription.CID = ?");
            $stmt->execute([$cid]);
            while ($row = $stmt->fetch()) {
                array_push($araDesc, $row);
            }

            $conn = null;
            return authCheck('landmarks.twig', $this, $req, $response, ['cid' => (int)$req->getAttribute('cid'), 'numBadge' => $numBadge, 'numCount' => $landmarkCount, 'incomingData' => 1, 'landmarks' => json_encode($ara), 'landmarkDesc' => json_encode($araDesc)]);
        } else {
            $conn = null;
            return authCheck('landmarks.twig', $this, $req, $response, ['cid' => (int)$req->getAttribute('cid'), 'numBadge' => $numBadge, 'numCount' => $landmarkCount, 'incomingData' => 0]);
        }
    });

    $app->get('/view/{cid}', function (Request $request, Response $response, $args) {
        return authCheck('view.twig', $this, $request, $response, ['cid' => (int)$request->getAttribute('cid')]);
    });

    $app->get('/edit/{cid}', function (Request $request, Response $response, $args) {
        $cid = (int)$request->getAttribute('cid');
        if (canEdit($response, $cid)) {
            $conn = connect_db();
            $stmt = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
            $stmt->execute([$cid]);
            $result = $stmt->fetch();
            $name = $result['Name'];
            $abbreviation = $result['Abbreviation'];
            $description = $result['Description'];
            $numberOfLandmarks = $result['NumberOfLandmarks'];
            $isOrder = $result['IsOrder'];
            
            return authCheck('edit.twig', $this, $request, $response, ['cid'=>$cid, 'name'=>$name, 'abbreviation'=>$abbreviation, 'description'=>$description,
            'numberOfLandmarks'=>$numberOfLandmarks, 'isOrder'=>$isOrder]);
        } else {
            echo "hey!";
        }
    });

    $app->get('/awards/{cid}', function (Request $request, Response $response, $args) {
        $cid = (int)$request->getAttribute('cid');
        return authCheck('awards.twig', $this, $request, $response, ['cid'=> $cid]);
    });
    
    $app->get('/edit/awards/{cid}', function (Request $request, Response $response, $args) {
        $cid = (int)$request->getAttribute('cid');
        if(canEdit($response, $cid)) {
            $conn = connect_db();
            $stmt = $conn->prepare("SELECT * FROM Awards WHERE CID = ?;");
            $stmt->execute([$cid]);
            $result = $stmt->fetch();
            $name = $result['Name'];
            $locationName = $result['LocationName'];
            $long = $result['Longitude'];
            $lat = $result['Latitude'];
            $optionalConditions = $result['optionalConditions'];
            
            return authCheck('editAwards.twig', $this, $request, $response, ['cid'=> $cid, 'name'=>$name, 'locationName'=>$locationName, 'longitude'=>$long,
            'latitude'=>$lat, 'optionalConditions'=>$optionalConditions]);
        } else {
        		echo "hey!";
        }
    });
?>