<?php


namespace DingRobotTest;


use DingRobot\Message\LinkMessage;
use DingRobot\Message\MarkdownMessage;
use DingRobot\Message\TextMessage;
use DingRobot\RobotInterface;
use DingRobotTest\Robot\Test2Robot;
use DingRobotTest\Robot\TestRobot;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class RobotTest extends TestCase
{
    protected $successResult = ['errmsg' => 'ok', 'errcode' => 0];

    /**
     * @var RobotInterface
     */
    protected $robot;

    /**
     * @var RobotInterface
     */
    protected $robot2;

    protected function setUp()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('request')->andReturn((new Response(200, [], json_encode($this->successResult))));
        /* @var ClientInterface $client */
        $this->robot = new TestRobot($client);
        $this->robot2 = new Test2Robot($client);
    }

    public function testTextMessage()
    {
        $result = $this->robot->send(new TextMessage('这是测试消息'));
        $this->assertEquals($this->successResult, $result);
    }

    public function testLinkMessage()
    {
        $result = $this->robot->send(new LinkMessage('这是标题', '这是内容', 'http://www.baidu.com'));
        $this->assertEquals($this->successResult, $result);
    }

    public function testMarkdownMessage()
    {
        $text = <<<MARKDOWN
#### 杭州天气 @156xxxx8827\n
> 9度，西北风1级，空气良89，相对温度73%\n\n
> ![screenshot](https://gw.alipayobjects.com/zos/skylark-tools/public/files/84111bbeba74743d2771ed4f062d1f25.png)\n
> ###### 10点20分发布 [天气](http://www.thinkpage.cn/) \n
MARKDOWN;
        $result = $this->robot->send(new MarkdownMessage('这是标题', $text));
        $this->assertEquals($this->successResult, $result);
    }

    /**
     * @depends testTextMessage
     */
    public function testMultiRobots()
    {
        $this->assertEquals($this->successResult, $this->robot2->send(new TextMessage('我是'.$this->robot2->getName())));
    }
}