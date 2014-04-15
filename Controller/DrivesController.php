<?php
App::uses('GoogleApiAppController', 'GoogleApi.Controller');

/**
 * Drives Controller
 */
class DrivesController extends GoogleApiAppController {

    /**
     * Components
     * @var type 
     */
    public $components = array('RequestHandler');

    public function index($folderId = null) {
        if (isset($_GET['code'])) {
            $this->Drive->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Session->check('GoogleApi.access_token')) {
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            if ($folderId) {
                $files = $this->Drive->getFilesByFolder($folderId);
            } else {
                $files = $this->Drive->getAllFiles();
            }

            $folders = $this->Drive->getAllFolder();
            //pr($files); exit;
        } else {
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
        $this->set('folders', $folders);
        $this->set('files', $files);
    }

    public function view($id = NULL) {
        if (isset($_GET['code'])) {
            $this->Drive->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Session->check('GoogleApi.access_token')) {
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            $file = $this->Drive->getFile($id);
            $fileContent = $this->Drive->downloadFile($file);

            //pr($file); exit;
        } else {
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
        $this->set('file', $file);
        $this->set('fileContent', $fileContent);
    }

    public function download($id = null) {
        $this->autoRender = false;

        if (isset($_GET['code'])) {
            $this->Drive->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Session->check('GoogleApi.access_token')) {
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            $file = $this->Drive->getFile($id);
            $fileContent = $this->Drive->downloadFile($file);
            $filename = WEBROOT_DIR . "/temp/" . time();
            $tempFile = new File($filename, true, 0777);
            $tempFile->write($fileContent, 'w', true);
            $tempFile->close();
            $this->response->file($filename);

            //pr($file); exit;
        } else {
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
    }

    public function add() {
        if ($this->Session->check('GoogleApi.access_token')) {
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            if ($this->request->is('post')) {
                if ($this->request->data['Drive']['file']['tmp_name'] != '' && is_uploaded_file($this->data['Drive']['file']['tmp_name'])) {
                    $name = $this->request->data['Drive']['file']['name'];
                    $description = $this->request->data['Drive']['description'];
                    $folder = $this->request->data['Drive']['folder'];
                    $tmp_name = $this->request->data['Drive']['file']['tmp_name'];

                    $type = $this->data['Drive']['file']['type'];
                    $size = $this->data['Drive']['file']['size'];
                    //echo $type;die;
                    //validate file type
                    $validate = true;
                    if ($validate) {
                        $createdFile = $this->Drive->uploadFile($name, $description, $folder, $type, $tmp_name);
                        //pr($createdFile); die;
                        if ($createdFile) {
                            $this->Session->setFlash(__('The file has been saved.'));
                            return $this->redirect(array('action' => 'index'));
                        } else {
                            $this->Session->setFlash(__('The file could not be saved. Please, try again.'));
                        }
                    } else {
                        $this->Session->setFlash(__('The file could not be saved. Please, try again.'));
                    }
                }
            }
            $folders = $this->Drive->getAllFolder();
            $foldersList = array();
            if (!empty($folders)) {
                foreach ($folders as $folder) {
                    $foldersList[$folder['id']] = $folder['title'];
                }
            }

            $this->set('folders', $foldersList);
        } else {
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
    }

    function delete($id) {
        $this->autoRender = false;

        if (isset($_GET['code'])) {
            $this->Drive->authenticateApi($_GET['code']);
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Session->check('GoogleApi.access_token')) {
            $accessToken = $this->Session->read('GoogleApi.access_token');
            $this->Drive->setAccessToken($accessToken);
            $result = $this->Drive->deleteFile($id);
            //If successful, this method returns an empty response body.
            //pr($result); exit;
            if (empty($result)) {
                $this->Session->setFlash(__('The file has been deleted.'));
            } else {
                $this->Session->setFlash(__('The file could not be deleted. Please, try again.'));
            }
            return $this->redirect(array('action' => 'index'));
        } else {
            $url = $this->Drive->createUrl();
            $this->redirect($url);
        }
    }

    function oauth2callback() {
        
    }

}

?>
