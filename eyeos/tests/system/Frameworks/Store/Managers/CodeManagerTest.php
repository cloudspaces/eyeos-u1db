<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/02/14
 * Time: 13:12
 */

class CodeManagerTest extends PHPUnit_Framework_TestCase
{
    private $codeProviderMock;
    private $sut;
    public function setUp()
    {
        $this->codeProviderMock = $this->getMock('CodeProvider');
        $this->sut = new CodeManager($this->codeProviderMock);

    }

    public function tearDown()
    {

    }

    /**
     *method:getEncryption
     * when:called
     * with: plainData
     * should: returnEncryptData
     */
    public function test_getEncryption_called_plainData_returnEncryptData()
    {
        $this->codeProviderMock->expects($this->once())
            ->method('getEncryption')
            ->with("ABCDEFG")
            ->will($this->returnValue("ABCDEFG"));

        $this->sut->getEncryption("ABCDEFG");
    }

    public function test_getDecryption_called_encryptData_returnPlainData()
    {
        $this->codeProviderMock->expects($this->once())
            ->method('getDecryption')
            ->with("ABCDEFG")
            ->will($this->returnValue("ABDCDEFG"));
        $this->sut->getDecryption("ABCDEFG");
    }


}


?>