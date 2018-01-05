<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 11/27/2017
 * Time: 6:03 PM
 */

namespace Magpiehunt\Controllers\Api;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Magpiehunt\Controllers\Controller as Controller;

class FirebaseController extends Controller
{
    protected $firebase;

    /**
     * @return \Kreait\Firebase
     */

    public function __construct($container)
    {
        parent::__construct($container);
        $this->firebase = (new Factory)
            ->withServiceAccountAndApiKey($this->getServiceAccount(), $this->getAPIKey())
            ->withDatabaseUri('https://magpie-3d047.firebaseio.com')
            ->create();
        return $this->firebase;
    }

    function getFirebaseDB()
    {
        $fbdatabase = $this->firebase->getDatabase();
    }
    function getServiceAccount()
    {
        return ServiceAccount::fromJsonFile(__DIR__ . '/../../../bootstrap/firebase-admin-credentials.json');
    }
    function getAPIKey()
    {
        return file_get_contents(__DIR__ . "/../../../bootstrap/ApiKey");
    }

   /* public function getFirebase(): \Kreait\Firebase
    {
        return $this->firebase;
    }
    public function authFirebase()
    {
        $auth = $this->firebase->getAuth();
    }
*/
}