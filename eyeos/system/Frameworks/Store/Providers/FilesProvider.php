<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/03/14
 * Time: 11:28
 */

class FilesProvider
{
    public function createFile($path,$isFolder = false)
    {
        try {
            $node = FSI::getFile($path);

            if($isFolder) {
                return $node->mkdir();
            } else {
                return $node->createNewFile();
            }

        }catch (Exception $e){
            return false;
        }
    }

    public function deleteFile($path,$isFolder = false)
    {
        try {
            $node = FSI::getFile($path);
            return $node->delete($isFolder);

        } catch (Exception $e) {
            return false;
        }
    }

    public function renameFile($path,$fileName) {
        try {
            $node = FSI::getFile($path);
            return $node->renameTo($fileName);

        }catch (Exception $e) {
            return false;
        }
    }
}

?>