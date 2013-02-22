<?php
namespace KochTest\Session;

use Koch\Session\FlashMessages;

class FlashMessagesTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Flashmessages::reset();
    }

    /**
     * @covers Koch\Session\FlashMessages::setMessage
     */
    public function testSetMessage()
    {
        FlashMessages::setMessage('MyMessage', 'error');

        $this->assertArrayHasKey('error', $_SESSION['user']['flashmessages']);
        $this->assertEquals('MyMessage', $_SESSION['user']['flashmessages']['error'][0]);
    }

     /**
     * @covers Koch\Session\FlashMessages::setErrorMessage
     */
    public function testSetErrorMessage()
    {
        FlashMessages::setErrorMessage('OneErrorMessage');

        $this->assertArrayHasKey('error', $_SESSION['user']['flashmessages']);
        $this->assertEquals('OneErrorMessage', $_SESSION['user']['flashmessages']['error'][0]);
    }

    /**
     * @covers Koch\Session\FlashMessages::getMessages
     */
    public function testGetMessages()
    {
         FlashMessages::setMessage('NoticeMessage', 'notice');
        // get message without type and unset
        $this->assertTrue(is_array(FlashMessages::getMessages()));
        $this->assertCount(1, FlashMessages::getMessages());

        // get messages by type and no unset
        FlashMessages::setMessage('NoticeMessage', 'notice');
        FlashMessages::setMessage('DebugMessage', 'debug');
        FlashMessages::setMessage('DebugMessage2', 'debug');
        $r = FlashMessages::getMessages('debug');
        $this->assertCount(2, $r);
        $this->assertEquals('DebugMessage', $r[0]);
        $this->assertEquals('DebugMessage2', $r[1]);
    }

    /**
     * @covers Koch\Session\FlashMessages::reset
     */
    public function testReset()
    {
        FlashMessages::reset();
        $this->assertTrue(is_array(FlashMessages::getMessages()));
        $this->assertCount(0, FlashMessages::getMessages());

    }

    /**
     * @covers Koch\Session\FlashMessages::render
     */
    public function testRender()
    {
        FlashMessages::setMessage('ErrorMessage', 'error');
        $this->assertEquals(
            $this->getFlashMessageRenderContent(),
            FlashMessages::render()
        );
    }

    public function getFlashMessageRenderContent()
    {
        return '<link rel="stylesheet" type="text/css" href="http://css/error.css" /><div id="flashmessage" class="flashmessage 0">ErrorMessage</div>';
    }
}
