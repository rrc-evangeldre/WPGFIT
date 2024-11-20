<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

class DropboxHelper {
    private $dropbox;

    public function __construct() {
        $app = new DropboxApp(DROPBOX_APP_KEY, DROPBOX_APP_SECRET, DROPBOX_ACCESS_TOKEN);
        $this->dropbox = new Dropbox($app);
    }

    public function uploadFile($localPath, $dropboxPath) {
        $dropboxFile = new Kunnu\Dropbox\DropboxFile($localPath);
        $uploadedFile = $this->dropbox->upload($dropboxFile, $dropboxPath, ['autorename' => true]);
        return $uploadedFile->getPathDisplay();
    }
}
?>
