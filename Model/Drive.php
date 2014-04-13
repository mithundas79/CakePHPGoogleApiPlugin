<?php

App::uses('GoogleApiAppModel', 'GoogleApi.Model');
App::uses('CakeSession', 'Model/Datasource');
set_include_path(APP . "Plugin" . DS . "GoogleApi" . DS . "Vendor" . DS . PATH_SEPARATOR . get_include_path());

require_once 'Google/Client.php';
require_once 'Google/Service/Calendar.php';
require 'Google/Service/Drive.php';

class Drive extends GoogleApiAppModel {

    public $client;
    public $clientId;
    public $clientSecret;
    public $clientRedirectUri;
    public $service;

    public function __construct() {
        $this->client = new Google_Client();
        $this->clientId = '975933459979-ae6r07mhf8a85bkfk85t2i5giman3ps8.apps.googleusercontent.com';
        $this->clientSecret = '02M4qq1wfiBodhpSLp41nnCn';
        $this->clientRedirectUri = 'http://local.plugindev.com/google_api/drives';
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->clientRedirectUri);

        #$service = new Google_Service_Drive($this->client);

        $this->client->addScope(Google_Service_Drive::DRIVE);
        $this->service = new Google_Service_Drive($this->client);
    }

    public function setSession() {
        CakeSession::write('GoogleApi.clientId', '31746962588-7mi3gt4s242lm4d9leiien5pcj1m64h4.apps.googleusercontent.com');
        CakeSession::write('GoogleApi.clientSecret', 'bZUDiyPXsQDdjGBWn8os-r-3');
        CakeSession::write('GoogleApi.clientRedirectUri', 'http://local.plugindev.com/google_api/drives');
    }

    function getAllFiles() {
        $result = array();
        $pageToken = NULL;

        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $parameters['q'] = "mimeType != 'application/vnd.google-apps.folder'";
                $files = $this->service->files->listFiles($parameters);

                $result = array_merge($result, $files->getItems());
                $pageToken = $files->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
        return $result;
    }

    function getAllFolder() {
        $result = array();
        $pageToken = NULL;

        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $parameters['q'] = "mimeType = 'application/vnd.google-apps.folder'";
                $files = $this->service->files->listFiles($parameters);

                $result = array_merge($result, $files->getItems());
                $pageToken = $files->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
        return $result;
    }

    function getFile($fileId) {
        try {
            $file = $this->service->files->get($fileId);

            return $file;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }

        return FALSE;
    }

    function downloadFile($file) {
        $downloadUrl = $file->getDownloadUrl();
        if ($downloadUrl) {
            $request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
            $httpRequest = Google_Client::$io->authenticatedRequest($request);
            if ($httpRequest->getResponseHttpCode() == 200) {
                return $httpRequest->getResponseBody();
            } else {
                // An error occurred.
                return null;
            }
        } else {
            // The file doesn't have any content stored on Drive.
            return null;
        }
    }

    function getParents($fileId = 0) {
        $results = array();
        try {
            $parents = $this->service->parents->listParents($fileId);

            foreach ($parents->getItems() as $parent) {
                $results[] = $parent;
            }
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
        return $parent;
    }

    function getFilesByFolder($folderId) {
        $pageToken = NULL;
        $result = array();
        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $parameters['q'] = "mimeType != 'application/vnd.google-apps.folder'";
                $children = $this->service->children->listChildren($folderId, $parameters);
                $result = array_merge($result, $children->getItems());
               
                $pageToken = $children->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
        return  $result;
    }

    public function createUrl() {
        return $this->client->createAuthUrl();
    }

    public function authenticateApi($code) {
        $this->client->authenticate($code);
        $accessToken = $this->client->getAccessToken();
        CakeSession::write('GoogleApi.access_token', $accessToken);
    }

    public function setAccessToken($accessToken) {
        $this->client->setAccessToken($accessToken);
    }

}

?>
