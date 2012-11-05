<?php
namespace Koch\Session;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-11-04 at 23:39:51.
 */
class FlashMessagesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Koch\Session\FlashMessages::setMessage
     */
    public function testSetMessage()
    {
        FlashMessages::setMessage('error', 'ErrorMessage');

        $this->assertArrayHasKey('error', $_SESSION['user']['flashmessages']);
        $this->assertEquals('ErrorMessage', $_SESSION['user']['flashmessages']['error'][0]);
    }

    /**
     * @covers Koch\Session\FlashMessages::getMessages
     */
    public function testGetMessages()
    {
        // get message without type and unset
        $this->assertTrue(is_array(FlashMessages::getMessages()));
        $this->assertCount(1, FlashMessages::getMessages());

        // get messages by type and no unset
        FlashMessages::setMessage('notice', 'NoticeMessage');
        FlashMessages::setMessage('debug', 'DebugMessage');
        FlashMessages::setMessage('debug', 'DebugMessage2');
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
        FlashMessages::setMessage('error', 'ErrorMessage');
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
