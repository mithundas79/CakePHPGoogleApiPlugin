<?php

App::uses('GoogleApiAppModel', 'GoogleApi.Model');
App::uses('CakeSession', 'Model/Datasource');
set_include_path(APP . "Plugin" . DS . "GoogleApi" . DS . "Vendor" . DS . PATH_SEPARATOR . get_include_path());

require_once 'Google/Client.php';
require_once 'Google/Service/Calendar.php';
require 'Google/Service/Drive.php';

class Calendar extends GoogleApiAppModel {

    public $client;
    public $clientId;
    public $clientSecret;
    public $clientRedirectUri;
    public $service;

    public function __construct() {
        $this->client = new Google_Client();
        $this->clientId = '975933459979-ae6r07mhf8a85bkfk85t2i5giman3ps8.apps.googleusercontent.com';
        $this->clientSecret = '02M4qq1wfiBodhpSLp41nnCn';
        $this->clientRedirectUri = 'http://local.plugindev.com/google_api/calendars';
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->clientRedirectUri);

        #$service = new Google_Service_Drive($this->client);

        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->service = new Google_Service_Calendar($this->client);
    }

    public function setSession() {
        CakeSession::write('GoogleApi.clientId', '31746962588-7mi3gt4s242lm4d9leiien5pcj1m64h4.apps.googleusercontent.com');
        CakeSession::write('GoogleApi.clientSecret', 'bZUDiyPXsQDdjGBWn8os-r-3');
        CakeSession::write('GoogleApi.clientRedirectUri', 'http://local.plugindev.com/google_api/calendars');
    }

    function getCalendars($params = array()) {
        $result = array();
        $pageToken = NULL;

        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $parameters = array_merge($parameters, $params);
                $calendarList = $this->service->calendarList->listCalendarList($parameters);

                $result = array_merge($result, $calendarList->getItems());
                $pageToken = $calendarList->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
        return $result;
    }

    function getCalendar($id) {
        try {
            return $this->service->calendarList->get($id);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
        return FALSE;
    }

    function getCalendarEvent($calendarId = 'primary') {
        $result = array();
        $pageToken = NULL;

        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $events = $this->service->events->listEvents($calendarId, $parameters);

                $result = array_merge($result, $events->getItems());
                $pageToken = $events->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
        return $result;
    }
    
    function getJsonEvents($calendarId = 'primary'){
        $events = $this->getCalendarEvent($calendarId);
        
        $results = array();
        if($events){
            foreach ($events AS $key=>$event){
                $results[$key]['id'] = $event->id;
                $results[$key]['title'] = $event->summary;
                //$results[$key]['allDay'] = $event->id;
                $results[$key]['start'] = (isset($event['data']['start']['dateTime']))?$event['data']['start']['dateTime']:$event['data']['start']['date'];
                $results[$key]['end'] = (isset($event['data']['end']['dateTime']))?$event['data']['end']['dateTime']:$event['data']['end']['date'];
                //$results[$key]['url'] = $event->id;
                //$results[$key]['editable'] = $event->id;
                $results[$key]['description'] = $event->description;
            }
        }
        return json_encode($results);
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
