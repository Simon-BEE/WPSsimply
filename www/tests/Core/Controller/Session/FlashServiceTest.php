<?php
namespace Tests\Core\Controller\Session;

use \PHPUnit\Framework\TestCase;
use \Core\Controller\Session\FlashService;
use \Core\Controller\Session\ArraySession;

class FlashServiceTest extends TestCase
{
    public function testGetMessagesWithoutMessage()
    {
        $messages = new FlashService(new ArraySession());
        $this->assertEquals(
            [],
            $messages->getMessages("success")
        );
    }
    
    public function testGetMessagesWithMessage()
    {
        $messages = new FlashService(new ArraySession());
        $messages->addSuccess('ça marche');
        $this->assertEquals(
            ['ça marche'],
            $messages->getMessages("success")
        );
    }
    public function testGetMessagesWithMessage2()
    {
        $messages = new FlashService(new ArraySession());
        $messages->addSuccess('pourquoi ça marche pas ?');
        $this->assertEquals(
            ['pourquoi ça marche pas ?'],
            $messages->getMessages("success")
        );
    }
    public function testGetMessagesWithMessageAlert()
    {
        $messages = new FlashService(new ArraySession());
        $messages->addAlert('ça marche pas !');
        $this->assertEquals(
            ['ça marche pas !'],
            $messages->getMessages("alert")
        );
    }

    public function testGetMessagesWith2MessageAlert()
    {
        $messages = new FlashService(new ArraySession());
        $messages->addAlert('ça marche pas !');
        $messages->addAlert('ça marche pas 2 !');
        $this->assertEquals(
            ['ça marche pas !', 'ça marche pas 2 !'],
            $messages->getMessages("alert")
        );
    }

    public function testHasMessagesWithoutMessage()
    {
        $messages = new FlashService(new ArraySession());
        $this->assertEquals(
            false,
            $messages->hasMessages("alert")
        );
    }

    public function testHasMessagesWithMessage()
    {
        $messages = new FlashService(new ArraySession());
        $messages->addAlert('ça marche pas !');
        $messages->addAlert('ça marche pas 2 !');
        $this->assertTrue($messages->hasMessages("alert"));
    }

    public function testgetMessagesWithMessages()
    {
        $messages = new FlashService(new ArraySession());

        $messages->addAlert('ça marche pas !');
        $this->assertEquals(
            ['ça marche pas !'],
            $messages->getMessages("alert")
        );
        $this->assertEquals(
            [],
            $messages->getMessages("alert")
        );
    }
}
