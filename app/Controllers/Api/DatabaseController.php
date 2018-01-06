<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/17/2017
 * Time: 2:13 PM
 */

namespace Magpiehunt\Controllers\Api;

use PDO;

use Magpiehunt\Controllers\Controller as Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$config = require __DIR__ . '/../../../bootstrap/config.php';


class DatabaseController extends Controller
{

    public function __construct($container)
    {
        parent::__construct($container);
    }

    function connect_db(){
        $config = require __DIR__ . '/../../../bootstrap/config.php';
        $server = $config->server;
        //echo $server;
        $user = $config->username;
        //echo $user;
        $pass = $config->password;
        //echo $pass;
        $database = $config->database;
        //echo $database;
        $connection = new PDO("mysql:host=" . $server . ";dbname=" . $database, $user, $pass);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $connection;
    }
    //gets all collections
    function getCollections(ServerRequestInterface $request){
        $conn = $this->connect_db();
        $query = "SELECT * FROM Collections WHERE Available = 1";
        $output = $conn->prepare($query);
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    function getCollection(ServerRequestInterface $request){
        $cid = (int)$request->getAttribute('cid');
        $conn = $this->connect_db();
        $query = "SELECT * FROM Collections WHERE cid = $cid";
        $output = $conn->prepare($query);
        $output->execute();
        $res = $output->fetch();
        echo json_encode($res);
        $conn = null;
    }
    //gets all landmarks with given cid
    function getLandmarks( ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $conn = $this->connect_db();
        $output = $conn->prepare("SELECT * FROM Landmarks WHERE cid = $cid ORDER BY 'Order' asc");
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    function getLandmarkImages(ServerRequestInterface $request, ResponseInterface $response)
    {
        $lid = (int)$request->getAttribute('lid');
        $conn = $this->connect_db();
        $output = $conn->prepare("SELECT * FROM LandmarksImages WHERE lid = $lid ORDER BY 'LID' asc");
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    function getCollectionImages(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $ara = array();
        $conn = $this->connect_db();
        $output = $conn->prepare("SELECT * FROM CollectionImages WHERE cid = $cid ORDER BY 'LID' asc");
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    function getAwards(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $ara = array();
        $conn = $this->connect_db();
        $output = $conn->prepare("SELECT * FROM Awards WHERE cid = $cid ORDER BY 'LID' asc");
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
        $conn = null;
    }
    function collectionDisble(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $conn = $this->connect_db();
        error_log(print_r($cid, TRUE));
        $output = $conn->prepare("UPDATE Collections SET Available = 0 WHERE CID = $cid");
        $output->execute();
        $conn = null;
    }
    function collectionEnable(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $conn = $this->connect_db();
        $output = $conn->prepare("UPDATE Collections SET Available = 1 WHERE CID = $cid");
        $output->execute();
        $conn = null;
    }
    function CollectionApprove(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = (int)$request->getAttribute('cid');
        $conn = $this->connect_db();
        $output = $conn->prepare("UPDATE Collections SET Available = 1 WHERE CID = $cid");
        $output->execute();
        $conn = null;
    }
    //TODO: Error checking and default values
    function addCollection(ServerRequestInterface $request, ResponseInterface $response)
    {
       // global $app;
        //$req = $app->request();
        //$body = json_decode($req->getBody());

        $name = $request->getParam('name');
        //if(empty($name)) throw new \UnexpectedValueException("Name must not be null");
        $city = $request->getParam('city');
        //if(empty($city)) $city = null;
        $state = $request->getParam('state');
        //if(empty($state)) $state = null;
        $zip = $request->getParam('zipcode');
        //if(empty($zip)) $zip = null;
        $desc = $request->getParam('description');
        //if(empty($desc)) throw new \UnexpectedValueException("Description must not be null");
        $ord = $request->getParam('ordered');
        //if(empty($ord)) $ord = null;
        $abv = $request->getParam('abbreviation');
        //if(empty($abv)) throw new \UnexpectedValueException("abbreviation must not be null");
        $avail = $request->getParam('available');
        if($avail == ''){ $avail = 1; }
        if($avail != '0' && $avail != '1') throw new \UnexpectedValueException("Value for avail must be 0 or 1");
        $rating = $request->getParam('rating');
        //if(empty($rating)) $rating = null;
        $sponsor = $request->getParam('sponsor');
        //if(empty($sponsor)) $sponsor = null;

        $conn = $this->connect_db();

        $query = "INSERT INTO Collections (Available, Name
               ,City
               ,State
               ,ZipCode
               ,Rating
               ,Description
               ,Ordered
               ,Abbreviation
               ,Sponsor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $output = $conn->prepare($query);
        $output->execute([$avail, $name, $city, $state, $zip, $rating, $desc, $ord, $abv, $sponsor]);
        $conn = null;
    }

    //TODO: Error checking and default values
    function addLandmark(ServerRequestInterface $request, ResponseInterface $response)
    {
        $cid = $request->getParam('cid');
        $name = $request->getParam('name');
        $lat = (double)$request->getParam('lat');
        $long = (double)$request->getParam('long');
        $desc = $request->getParam('desc');
        $qr = $request->getParam('qr');
        $pic = (int)$request->getParam('pic');
        $badge = (int)$request->getParam('badge');
        $order = (int)$request->getParam('order');

        $conn = $this->connect_db();

        $query = "INSERT INTO Landmarks (CID, LandmarkName
               ,Latitude
               ,Longitude
               ,LandmarkDescription
               ,QRCode
               ,PicID
               ,BadgeID
               ,OrderNum) VALUES ('$cid', '$name', '$lat', '$long', '$desc', '$qr', '$pic', '$badge', '$order')";
        $output = $conn->prepare($query);
        $output->execute();
        $conn = null;
    }
    function addCollectionImage(ServerRequestInterface $request, ResponseInterface $response)
    {

    }
    function addLandmarkImage(ServerRequestInterface $request, ResponseInterface $response)
    {

    }
    function addCollectionOwner(ServerRequestInterface $request, ResponseInterface $response)
    {

    }

    function uploadImage(ServerRequestInterface $request, ResponseInterface $response)
    {
        $files = $request->getUploadedFiles();

        if(empty($files['file']))
        {
            throw new \RuntimeException('Expected a new file');
        }
        $file = $files['file'];
        if($file->getError() === UPLOAD_ERR_OK)
        {
            $fileName = $file->getClientFilename();
            $file = moveTo(storage_path("/images/{$fileName}"));

            return $response->withJson(['result' => [
                'fileName' => $fileName
            ]])->withStatus(200);
        }

        return $response
            ->withJson([
                'error' => 'Nothing was uploaded'
            ])
            ->withStatus(415);
    }

}