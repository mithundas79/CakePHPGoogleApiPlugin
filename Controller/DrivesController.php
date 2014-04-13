<?php
App::uses('GoogleApiAppController', 'GoogleApi.Controller');

/**
 * Drives Controller
 */

class DrivesController extends GoogleApiAppController{
    /**
     * Components
     * @var type 
     */
    public $components = array('RequestHandler');
    
    public function index($folderId = null){
        if (isset($_GET['code'])) {
            $this->Drive->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if($this->Session->check('GoogleApi.access_token')){
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            if($folderId){
                $files = $this->Drive->getFilesByFolder($folderId);
            }else{
                $files = $this->Drive->getAllFiles();
            }
            
            $folders = $this->Drive->getAllFolder();
            //pr($files); exit;
        }else{
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
        $this->set('folders', $folders);
        $this->set('files', $files);
    }
    public function view($id=NULL){
        if (isset($_GET['code'])) {
            $this->Drive->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if($this->Session->check('GoogleApi.access_token')){
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            $file = $this->Drive->getFile($id);
            $fileContent = $this->Drive->downloadFile($file);
            
            //pr($file); exit;
        }else{
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
        $this->set('file', $file);
        $this->set('fileContent', $fileContent);
    }
    
    public function download($id=null){
        $this->autoRender = false;
          
        if (isset($_GET['code'])) {
            $this->Drive->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if($this->Session->check('GoogleApi.access_token')){
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            $file = $this->Drive->getFile($id);
            $fileContent = $this->Drive->downloadFile($file);
            $filename = WEBROOT_DIR."/temp/".time();
            $tempFile = new File($filename, true, 0777);
            $tempFile->write($fileContent, 'w', true);
            $tempFile->close();
            $this->response->file($filename);
            
            //pr($file); exit;
        }else{
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
    }
            
    function oauth2callback(){
        
    }
}

?>
