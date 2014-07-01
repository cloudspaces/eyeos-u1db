<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/06/14
 * Time: 16:29
 */

class CommentsManager
{
    private $accessorProvider;
    private $u1dbCredsManager;

    public function __construct(AccessorProvider $accessorProvider = NULL,U1DBCredsManager $u1DBCredsManager = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;

        if(!$u1DBCredsManager) $u1DBCredsManager = new U1DBCredsManager($this->accessorProvider);
        $this->u1dbCredsManager = $u1DBCredsManager;
    }

    public function createComment($id,$user,$text)
    {
        $result = json_decode('{"error":-1,"description":"Result comments not format JSON"}');
        $comment = new stdClass();
        $comment->type = 'create';
        $comment->metadata = new stdClass();
        $comment->metadata->id = $id;
        $comment->metadata->user = $user;
        $comment->metadata->text = $text;
        $data = $this->exerciseComments($comment);
        if($data !== NULL){
            $result = $data;
        }
        return $result;
    }

    public function deleteComment($id,$user,$time_created)
    {
        $result = json_decode('{"error":-1,"description":"Result comments not format JSON"}');
        $comment = new stdClass();
        $comment->type = 'delete';
        $comment->metadata = new stdClass();
        $comment->metadata->id = $id;
        $comment->metadata->user = $user;
        $comment->metadata->time_created = $time_created;
        $data = $this->exerciseComments($comment);
        if($data !== NULL){
            $result = $data;
        }
        return $result;
    }

    public function getComments($id)
    {
        $result = json_decode('{"error":-1,"description":"Result comments not format JSON"}');
        $comment = new stdClass();
        $comment->type = 'get';
        $comment->metadata = new stdClass();
        $comment->metadata->id = $id;
        $data = $this->exerciseComments($comment);
        if($data !== NULL){
            $result = $this->sortArrayComments($data,sizeof($data));
        }
        return $result;
    }

    private function sortArrayComments($list,$sizeList)
    {
        for($i=1;$i<$sizeList;$i++){
            for($j=0;$j<$sizeList-$i;$j++){
                if(intval($list[$j]->time_created) < intval($list[$j+1]->time_created)){
                    $aux=$list[$j+1];
                    $list[$j+1]=$list[$j];
                    $list[$j]=$aux;
                }
            }
        }
        return $list;
    }

    private function exerciseComments($comment)
    {
        $credentials = $this->u1dbCredsManager->callProcessCredentials();
        $comment->credentials = $credentials;
        $data = json_decode($this->accessorProvider->getProcessComments(json_encode($comment)));
        return $data;
    }
}

?>