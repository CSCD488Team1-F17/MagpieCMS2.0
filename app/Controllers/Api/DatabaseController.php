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

class DatabaseController extends Controller
{

    public function __construct($container)
    {
        parent::__construct($container);
    }

    function getLandmarkImages(ServerRequestInterface $request, ResponseInterface $response)
    {
        $lid = (int)$request->getAttribute('lid');
        $dbconn = new DBConnection($this->container);
        $conn = $dbconn->connect_db();
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
        $dbconn = new DBConnection($this->container);
        $conn = $dbconn->connect_db();
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
        $dbconn = new DBConnection($this->container);
        $conn = $dbconn->connect_db();
        $output = $conn->prepare("SELECT * FROM Awards WHERE cid = $cid ORDER BY 'LID' asc");
        $output->execute();
        $res = $output->fetchAll();
        echo json_encode($res);
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