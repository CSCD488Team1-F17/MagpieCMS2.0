<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

    // Helper functions
    function connect_db(){
        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';
        $connection = new PDO("mysql:host=$config->server;dbname=$config->database" ,$config->username, $config->password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }

	function badgeUpload($request, $lid) {
			$files = $request->getUploadedFiles();
			$cid = (int)$request->getParam('cid');

			if(empty($files['file1'])) {
				throw new Exception('Expected a newfile');
			}

			if(empty($files['file2'])) {
				throw new Exception('Expected a second newfile');
			}

			$newfile1 = $files['file1'];
			$newfile2 = $files['file2'];

			//File storage updates
			if($newfile1->getError() === UPLOAD_ERR_OK && $newfile2->getError() === UPLOAD_ERR_OK) {
				$uploadFileName1 = $newfile1->getClientFilename();
				$uploadFileName2 = $newfile2->getClientFilename();
				mkdir("../Resources/Images/$cid");//create directory if you can
				$newfile1->moveTo("../Resources/Images/$cid/$uploadFileName1");
				$newfile2->moveTo("../Resources/Images/$cid/$uploadFileName2");
			}

			//Database updates
			$imageType = substr($uploadFileName1, strpos($uploadFileName1, ".")+1);
			$conn = connect_db();
			$stmt = $conn->prepare("INSERT INTO LandmarkImages (FileLocation, ImageType) Values (?, ?)");
			$stmt->execute([$cid, $uploadFileName1, $imageType]);

			$stmt = $conn->prepare("UPDATE Landmarks SET Badge = ? WHERE LID = ?;");
			$stmt->execute($uploadFileName2, $lid);

			$output = $conn->query("SELECT MAX(PicID) AS MaxPid FROM LandmarkImages;");
			while($row = $output->fetch()) {
				$pid = $row['MaxPid'];
			}

			$conn = null;
			return $pid;
	}

	function superbadgeUpload($request){
        $files = $request->getUploadedFiles();
        $cid = (int)$request->getParam("cid");
        $pid;
        if (empty($files['newfile'])) {
            throw new Exception('Expected a newfile');
        }

		//File storage updates
        $newfile = $files['newfile'];
        if ($newfile->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $newfile->getClientFilename();
			mkdir("../Resources/Images/$cid");//create directory if you can
            $newfile->moveTo("../Resources/Images/$cid/$uploadFileName");
        }

		//Database updates
		$imageType = substr($uploadFileName, strpos($uploadFileName, ".")+1);
        $conn = connect_db();
        $stmt = $conn->prepare("INSERT INTO CollectionImages (CID, FileLocation, ImageType) Values (?, ?, ?)");
		$stmt->execute([$cid, $uploadFileName, $imageType]);
        $output = $conn->query("SELECT MAX(PicID) AS MaxPid FROM CollectionImages;");
        while($row = $output->fetch()) {
            $pid = $row['MaxPid'];
        }
        $conn = null;

        return $pid;
    }

    //api calls

	/* Most of the /api/ calls are endpoints for android teams to get a hold of corresponding information */
    $app->get('/api/collection/', function (Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
	    $output = $conn->query("SELECT * FROM Collections WHERE STATUS = 1;");
        while($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    });

    $app->get('/api/collection/{cid}', function (Request $request, Response $response){
        $conn = connect_db();
        $cid = (int)$request->getAttribute('cid');
        $stmt = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
        $stmt->execute([$cid]);
        $output = $stmt->fetch();
            echo json_encode($output);
        $conn = null;
    });

    $app->post('/api/collection/disable', function (Request $request, Response $response){
        $conn = connect_db();
        $json = $request->getBody();
        $data = json_decode($json, true);

        $cid = $data['cid'];
        error_log(print_r($cid, TRUE));
        $stmt = $conn->prepare("UPDATE Collections SET Status = ? WHERE CID = ?");
        $stmt->execute([0, $cid]);
        $conn = null;
    });

    $app->post('/api/collection/enable', function (Request $request, Response $response){
        $conn = connect_db();
        $json = $request->getBody();
        $data = json_decode($json, true);

        $cid = $data['cid'];
        error_log(print_r($cid, TRUE));
        $stmt = $conn->prepare("UPDATE Collections SET Status = ? WHERE CID = ?");
        $stmt->execute([1, $cid]);
        $conn = null;
    });

    $app->post('/api/collection/approve', function (Request $request, Response $response){
        $conn = connect_db();
        $json = $request->getBody();
        $data = json_decode($json, true);

        $cid = $data['cid'];
        error_log(print_r($cid, TRUE));
        $stmt = $conn->prepare("UPDATE Collections SET Status = ? WHERE CID = ?");
        $stmt->execute([2, $cid]);
        $conn = null;
    });

    $app->get('/api/landmark/{lid}', function (Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $lid = (int)$request->getAttribute('lid');
        $stmt = $conn->prepare("SELECT DISTINCT * FROM Landmarks INNER JOIN LandmarkDescription ON LandmarkDescription.DesID = Landmarks.DescID AND Landmarks.LID = ? AND LandmarkDescription.LID = ?;");
        $stmt->execute([$lid, $lid]);
        while($row = $stmt->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    });

    $app->get('/api/landmark/all/{cid}', function (Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $cid = (int)$request->getAttribute('cid');
        $stmt = $conn->prepare("SELECT * FROM Landmarks LEFT JOIN LandmarkDescription ON LandmarkDescription.DesID = Landmarks.DescID INNER JOIN CollectionLandmarks ON CollectionLandmarks.LandmarkID = Landmarks.LID WHERE CollectionLandmarks.CollectionID = ?;");
        $stmt->execute([$cid]);
        while($row = $stmt->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    });

    $app->post('/api/user/app', function (Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client(); //Composer is used to set up Google_Client() configuration stuff
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM AppUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();

            if($output['UID'] != $userid){
                $stmt = $conn->prepare("INSERT INTO AppUserData (UID) VALUES (?)");
                $stmt->execute([$userid]);
            } else{
                echo "found em!";
            }
        } else {
            return $response->withStatus(300);
        }

        $conn = null;
        return $response->withStatus(200);
    });

    $app->post('/api/user/app/progress/pull', function (Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userID = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM AppUserData WHERE UID = ?;");
            $stmt->execute([$userID]);
            $output = $stmt->fetch();

            if($output['UID'] != $userID){
                echo "you dont exist!";
            } else{
                $uid = $output['UserID'];
                $stmt = $conn->prepare("SELECT CollectionID FROM UserCollectionInprogress WHERE UserID = ?;");
                $stmt->execute([$uid]);

                $collections = array();
                while($row = $stmt->fetch()) {
                    $cid = $row['CollectionID'];
                    array_push($collections, $cid);
                }

                $landmarks = array();
                $i = 0;
                foreach($collections as $cid){
                    $landmarks[$i] = array();

                    $stmt = $conn->prepare("SELECT LandmarkID FROM UserLandmarksSeen WHERE UserID = ? AND CollectionID = ?;");
                    $stmt->execute([$uid, $cid]);
                    while($row = $stmt->fetch()) {
                        $lid = $row['LandmarkID'];
                        array_push($landmarks[$i], $lid);
                    }
                    $i += 1;
                }

                $objArray = array();
                $i = 0;
                foreach($collections as $cid){
                    $objArray[$i] = (object) ["cid" => $cid, "landmarks" => $landmarks[$i]];
                }

                return $response->withJson($objArray);
            }
        } else {
            error_log(print_r("Sending back a 300", TRUE));
            return $response->withStatus(300);
        }

        $conn = null;
        return $response->withStatus(200);
    });

    $app->post('/api/user/app/progress/push', function (Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);
        $collections = $data['collections'];

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userID = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM AppUserData WHERE UID = ?;");
            $stmt->execute([$userID]);
            $output = $stmt->fetch();

            if($output['UID'] != $userID){
                echo "you dont exist!";
            } else{
                $uid = $output['UserID'];
                $stmt = $conn->prepare("SELECT CollectionID FROM UserCollectionInprogress WHERE UserID = ?;");
                $stmt->execute([$uid]);

                $array = array();
                while($row = $stmt->fetch()) {
                    $cid = $row['CollectionID'];
                    array_push($array, $cid);
                }

                foreach($collections as $c){
                    if(in_array($c['cid'], $array)){
                        //do an update
                        $landmarks = $c['landmarks'];
                        $i = 1;
                        foreach($landmarks as $seen){
                            if($seen){
                                $stmt = $conn->prepare("INSERT IGNORE INTO UserLandmarksSeen (UserID, LandmarkID, CollectionID) VALUES (?, ?, ?)");
	                	        $stmt->execute([$uid, $i, $c['cid']]);
                            }
                            $i += 1;
                        }
                    } else {
                        //do an insert
                        $stmt = $conn->prepare("INSERT INTO UserCollectionInprogress (UserID, CollectionID, NumberOfLandmarksSeen) VALUES (?, ?, ?)");
	                	$stmt->execute([$uid, $c['cid'], 0]);

                        $landmarks = $c['landmarks'];
                        $i = 1;
                        foreach($landmarks as $seen){
                            if($seen){
                                $stmt = $conn->prepare("INSERT INTO UserLandmarksSeen (UserID, LandmarkID, CollectionID) VALUES (?, ?, ?)");
	                	        $stmt->execute([$uid, $i, $c['cid']]);
                            }
                            $i += 1;
                        }
                    }
                }
            }
        } else {
            error_log(print_r("Sending back a 300", TRUE));
            return $response->withStatus(300);
        }

        $conn = null;
        return $response->withStatus(200);
    });

    $app->post('/api/user/web', function (Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();

            if($output['UID'] == $userid){
                $conn = null;
                return $response->withJson($output);
            } else{
                return $response->withStatus(300);
            }
        } else {
            return $response->withStatus(300);
        }
    });

    $app->post('/api/user/web/collections', function (Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $userID = $data['UserID'];
        error_log(print_r($userID, TRUE));
        $conn = connect_db();
        if($userID == 0){
            $stmt = $conn->prepare("SELECT CollectionID FROM UserMadeCollectionList;");
            $stmt->execute([$userID]);
        } else {
            $stmt = $conn->prepare("SELECT CollectionID FROM UserMadeCollectionList WHERE UserID = ?;");
            $stmt->execute([$userID]);
        }

        $result = array();
        while($row = $stmt->fetch()) {
            $cid = $row['CollectionID'];
            $stmt2 = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
            $stmt2->execute([$cid]);
            $output = $stmt2->fetch();
            array_push($result, $output);
        }

        return $response->withJson($result);
    });

	/* This is the post route that gets triggered through the ajax in the create.twig page. This will take all the information in the form
	   and store it in the database including the superbadge which is a call to the helper function above. */
	$app->post('/database/collection', function(Request $request){
		$cid =(int)$request->getParam("cid");
		$name = $request->getParam("name");
        $abv = $request->getParam("abv");
		$description = $request->getParam("summary");
		$numberOfLandmarks = (int)$request->getParam("numBadge");
        $isOrdered = (int)$request->getParam("ordered");
        $idToken = $request->getParam("idToken");

        error_log(print_r($idToken, TRUE));

		$conn = connect_db();
		$stmt = $conn->prepare("INSERT INTO Collections (Name, Abbreviation, Description, NumberOfLandMarks, IsOrder) VALUES (?, ?, ?, ?, ?)");

		$stmt->execute([$name, $abv, $description, $numberOfLandmarks, $isOrdered]);

        $picID = (int)superbadgeUpload($request);
        $stmt = $conn->prepare("UPDATE Collections SET PicID = ? WHERE CID = ?");
        $stmt->execute([$picID, $cid]);

        $conn = null;

        $config = require dirname(__FILE__, 2) . '/../bootstrap/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope('openid');

        $payload = $client->verifyIdToken($idToken);
        if ($payload) {
            $userid = $payload['sub'];
            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();
            error_log(print_r($output['UserID'], TRUE));
            if($output['UID'] == $userid){
                $stmt = $conn->prepare("INSERT INTO UserMadeCollectionList (UserID, CollectionID) VALUES (?, ?);");
                $stmt->execute([$output['UserID'], $cid]);
            }

            $conn = null;
        } else {
            return $response->withStatus(300);
        }

        echo("success");
	});

	/* This is the post function for the edit.twig page and currently the suberbadge stuff is not implemented upon edit. */
	$app->post('/edit/collection', function(Request $request){
		$cid =(int)$request->getParam("cid");
		$name = $request->getParam("name");
		$abbreviation = $request->getParam("abbreviation");
		$description = $request->getParam("summary");
		$numberOfLandmarks = (int)$request->getParam("numBadge");
        $isOrder = (int)$request->getParam("ordered");
		//$picID

		$conn = connect_db();
		$stmt = $conn->prepare("UPDATE Collections SET Name = ?, Abbreviation = ?, Description = ?, NumberOfLandmarks = ?, IsOrder = ?, Status = ? WHERE CID = ?");
		$stmt->execute([$name, $abbreviation, $description, $numberOfLandmarks, $isOrder, 1, $cid]);
	});

	/* This function will check all nested files within a directory and compress all the images for distribution as a ZIP archive. We currently call the folder
		that contains the associated images for a collection the CID for said collection. All associated images will reside in Resources/Images/CID and when called,
		this function will return a zip with all of them it for the app team to use. */
	$app->get('/images/collection/{cid}', function (Request $request, Response $response){
        $conn = connect_db();
		$cid = (int)$request->getAttribute('cid');
		$fileName = "Collection".$cid."Images.zip";

		//Folder name for collection images assumed to be just the CID
		$rootPath = realpath("../Resources/Images/$cid");
		$zip = new ZipArchive();
		if ($zip->open($fileName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
			die ("An error occurred creating your ZIP file.");
		}

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY);

		foreach($files as $name => $file)
		{
			if(!$file->isDir())
			{
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($rootPath) + 1);
					$zip->addFile($filePath, 'Images/'.$relativePath);
			}
		}
		$zip->close();
		readFile($fileName);
		unlink($fileName);
		$conn = null;
		return $response->withHeader("Content-Type", "application/zip")
		->withHeader("Content-Disposition", "attachment; filename=$fileName")
		->withHeader("Cache-Control", "no-store,no-cache");
    });

	/* This is the post function that is invoked by the ajax in landmarks.twig and is used to insert badge information into the landmark associated tables. */
    $app->post('/database/landmark', function(Request $request) {
        $name = $request->getParam("name");
        $long = (double)$request->getParam("long");
        $lat = (double)$request->getParam("lat");
        $cid = (int)$request->getParam("cid");
        $creator = $request->getParam("creator");
        $url = $request->getParam("url");
        $description = $request->getParam("description");

        $conn = connect_db();
        $stmt = $conn->prepare("INSERT INTO Landmarks (Name, Longitude, Latitude, Creator, InfoLink) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $long, $lat, $creator, $url]);

        $lid;
        $output = $conn->query("SELECT MAX(LID) AS MaxLid FROM Landmarks;");
        while($row = $output->fetch()) {
            $lid = $row['MaxLid'];
        }

        $output = $conn->query("SELECT MAX(DesID) AS MaxDesID FROM LandmarkDescription;");
        while($row = $output->fetch()) {
            $desid = $row['MaxDesID'];
        }

        $stmt = $conn->prepare("INSERT INTO CollectionLandmarks (CollectionID, LandmarkID) VALUES (?, ?)");
        $stmt->execute([$cid, $lid]);

        $stmt = $conn->prepare("INSERT INTO LandmarkDescription (DesID, LID, CID, Description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$desid, $lid, $cid, $description]);

		$pid = (int)badgeUpload($request, $lid);
		$stmt = $conn->prepare("UPDATE Landmarks SET PicID = ? WHERE LID = ?;");
		$stmt->execute([$pid, $lid]);

        $conn = null;
        return $lid;
	});

    $app->post('/database/landmark/update/{lid}', function(Request $request){
        $name = $request->getParam("name");
        $long = (double)$request->getParam("long");
        $lat = (double)$request->getParam("lat");
        $cid = (int)$request->getParam("cid");
        $creator = $request->getParam("creator");
        $url = $request->getParam("url");
        $description = $request->getParam("description");

        $conn = connect_db();
        $stmt = $conn->prepare("UPDATE Landmarks SET Name = ?, Longitude = ?, Latitude = ?, Creator = ?, InfoLink = ? WHERE LID = ?");
        $stmt->execute([$name, $long, $lat, $creator, $url, (int)$request->getAttribute("lid")]);

        $stmt = $conn->prepare("UPDATE LandmarkDescription SET Description = ? WHERE LID = ? AND CID = ?");
        $stmt->execute([$description, (int)$request->getAttribute("lid"), $cid]);
    });

	/* The post call invoked by the awards.twig page and is used for award information insertion. */
	$app->post('/add/awards', function(Request $request){
		$cid =(int)$request->getParam("cid");
		$name = $request->getParam("name");
		$lat = $request->getParam("latitude");
		$long = $request->getParam("longitude");
		$optionalConditions = $request->getParam("optionalConditions");
		$conn = connect_db();
		$stmt = $conn->prepare("INSERT INTO Awards (CID, Name, Latitude, Longitude, optionalConditions) VALUES (?, ?, ?, ?, ?);");
		$stmt->execute([$cid, $name, $lat, $long, $optionalConditions]);
		$conn = null;
	});

	/* The post call for additional badges to be added */
    $app->post('/collection/newBadge', function($request){
        $numBadge = (int)$request->getParam("numBadge") + 1;
        $cid = (int)$request->getParam("cid");
        $conn = connect_db();
        $stmt = $conn->prepare("UPDATE Collections SET NumberOfLandmarks = ? WHERE CID = ?");
        $stmt->execute([$numBadge, $cid]);
        $conn = null;
    });
?>
