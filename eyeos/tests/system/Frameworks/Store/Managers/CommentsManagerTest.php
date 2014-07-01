<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/06/14
 * Time: 16:11
 */

class CommentsManagerTest extends PHPUnit_Framework_TestCase
{
    private $accessorProviderMock;
    private $u1dbCredsManagerMock;
    private $sut;
    private $credentials;

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->u1dbCredsManagerMock = $this->getMock('U1DBCredsManager');
        $this->credentials = '{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}';
        $this->sut = new CommentsManager($this->accessorProviderMock,$this->u1dbCredsManagerMock);
    }

    public function tearDown()
    {
        $this->credentials = null;
        $this->sut = null;
        $this->accessorProviderMock = null;
    }

    /**
     * method: createComment
     * when: called
     * with: idAndUserAndText
     * should: returnMetadataCorrect
     */
    public function test_createComment_called_idAndUserAndText_returnMetadataCorrect()
    {
        $id = "93509385";
        $user = "eyeos";
        $text =  "Por favor, modificar la descripcion del punto 1.2";
        $expected = '{"id":"' . $id . '","user":"' . $user . '","time_created":"20140630163000","status":"NEW","text":"Por favor, modificar la descripcion del punto 1.2"}';
        $this->exerciseCreateComment($id,$user,$text,$expected);
    }

    /**
     * method: createComment
     * when: called
     * with: idAndUserAndText
     * should: returnError
     */
    public function test_createComment_called_idAndUserAndText_returnError()
    {
        $id = "93509385";
        $user = "eyeos";
        $text =  "Por favor, modificar la descripcion del punto 1.2";
        $expected = '{"error":-1,"description":"Bad parameters"}';
        $this->exerciseCreateComment($id,$user,$text,$expected);

    }

    /**
     * method: deleteComment
     * when: called
     * with: idAndUserAndTimeCreated
     * should: returnCorrect
     */
    public function test_deleteComment_called_idAndUserAndTimeCreated_returnCorrect()
    {
        $id = "93509385";
        $user = "eyeos";
        $time_created = "20140630143000";
        $expected = '{"status":"OK"}';
        $this->exerciseDeleteComment($id,$user,$time_created,$expected);
    }

    /**
     * method: deleteComment
     * when: called
     * with: idAndUserAndTimeCreated
     * should: returnError
     */
    public function test_deleteComment_called_idAndUserAndTimeCreated_returnError()
    {
        $id = "93509385";
        $user = "eyeos";
        $time_created = "20140630143000";
        $expected = '{"status":"KO","error":-1}';
        $this->exerciseDeleteComment($id,$user,$time_created,$expected);

    }

    /**
     * method: getComments
     * when: called
     * with: id
     * should: returnMetadatas
     */
    public function test_getComments_called_id_returnMetadatas()
    {
        $id = "93509385";
        $commentsU1db='[{"id":"' . $id . '","user":"stacksync","time_created":"20140630113000","status":"NEW","text":"texto 1"},
                    {"id":"' . $id . '","user":"stacksync","time_created":"20140701140000","status":"NEW","text":"texto modificado"},
                    {"id":"' . $id . '","user":"stacksync","time_created":"20140701213000","status":"NEW","text":"texto nuevo"}]';

        $expected='[{"id":"' . $id . '","user":"stacksync","time_created":"20140701213000","status":"NEW","text":"texto nuevo"},
                    {"id":"' . $id . '","user":"stacksync","time_created":"20140701140000","status":"NEW","text":"texto modificado"},
                    {"id":"' . $id . '","user":"stacksync","time_created":"20140630113000","status":"NEW","text":"texto 1"}]';

        $this->exerciseGetComments($id,$expected,$commentsU1db);

    }

    /**
     * method: getComments
     * when: called
     * with: id
     * should: returnError
     */
    public function test_getComments_called_id_returnError()
    {
        $id = "93509385";
        $expected = '[{"error":-1,"description":"Bad parameters"}]';
        $this->exerciseGetComments($id,$expected);
    }

    private function exerciseCreateComment($id,$user,$text,$expected)
    {
        $comment = new stdClass();
        $comment->type = 'create';
        $comment->metadata = new stdClass();
        $comment->metadata->id = $id;
        $comment->metadata->user = $user;
        $comment->metadata->text = $text;
        $comment->credentials = $this->credentials;

        $this->u1dbCredsManagerMock->expects($this->once())
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessComments')
            ->with(json_encode($comment))
            ->will($this->returnValue($expected));

        $result = $this->sut->createComment($id,$user,$text);
        $this->assertEquals(json_decode($expected),$result);
    }

    private function exerciseDeleteComment($id,$user,$time_created,$expected)
    {
        $this->u1dbCredsManagerMock->expects($this->once())
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $comment = new stdClass();
        $comment->type = 'delete';
        $comment->metadata = new stdClass();
        $comment->metadata->id = $id;
        $comment->metadata->user = $user;
        $comment->metadata->time_created = $time_created;
        $comment->credentials = $this->credentials;
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessComments')
            ->with(json_encode($comment))
            ->will($this->returnValue($expected));

        $result = $this->sut->deleteComment($id,$user,$time_created);
        $this->assertEquals(json_decode($expected),$result);
    }

    private function exerciseGetComments($id,$expected,$commentsU1db = NULL)
    {
        if(!$commentsU1db) $commentsU1db = $expected;
        $comment = new stdClass();
        $comment->type = 'get';
        $comment->metadata = new stdClass();
        $comment->metadata->id = $id;
        $comment->credentials = $this->credentials;

        $this->u1dbCredsManagerMock->expects($this->once())
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessComments')
            ->with(json_encode($comment))
            ->will($this->returnValue($commentsU1db));

        $result = $this->sut->getComments($id);
        $this->assertEquals(json_decode($expected),$result);
    }

}

?>