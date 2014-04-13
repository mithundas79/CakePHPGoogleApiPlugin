<?php


class CalendarsController extends GoogleApiAppController{
    /**
     * Components
     * @var type 
     */
    public $components = array('RequestHandler');
    
    function index(){
        if (isset($_GET['code'])) {
            $this->Calendar->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if($this->Session->check('GoogleApi.access_token')){
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Calendar->setAccessToken($accessToken);
            $calendars = $this->Calendar->getCalendars(array('minAccessRole'=>'owner'));
            //echo '<h2>Calendar list</h2>';
            //pr($calendars); 
            //echo '<h2>Event list</h2>';
            //$events = $this->Calendar->getCalendarEvent();
            //pr($events);
            //exit;
        }else{
            $url = $this->Calendar->createUrl();
            $this->redirect($url);
        }
        $this->set('calendars', $calendars);
    }
    
    function view($id){
        if (isset($_GET['code'])) {
            $this->Calendar->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if($this->Session->check('GoogleApi.access_token')){
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Calendar->setAccessToken($accessToken);
            $calendar = $this->Calendar->getCalendar($id);
            echo '<h2>Calendar Summery</h2>';
            echo $calendar->summary;
            echo '<h2>json Event list</h2>';
            $events = $this->Calendar->getJsonEvents($id);
            pr($events);
            exit;
        }else{
            $url = $this->Calendar->createUrl();
            $this->redirect($url);
        }
        $this->set('calendar', $calendar);
    }
    
    function oauth2callback(){
        
    }
}

?>
