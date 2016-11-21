<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\GitHubWebHook\Tests\Event;

use Psr\Http\Message\RequestInterface;
use Swop\GitHubWebHook\Event\GitHubEvent;
use Swop\GitHubWebHook\Event\GitHubEventFactory;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;

/**
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class GitHubEventFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider buildDataProvider
     *
     * @param string      $type
     * @param array       $payload
     * @param GitHubEvent $expectedEvent
     */
    public function testBuild($type, array $payload, GitHubEvent $expectedEvent)
    {
        $factory = new GitHubEventFactory();

        $this->assertEquals($expectedEvent, $factory->build($type, $payload));
    }

    /**
     * @expectedException \Swop\GitHubWebHook\Exception\InvalidGitHubRequestPayloadException
     */
    public function testBuildFromRequestShouldFailIfPayloadIsNotJSON()
    {
        $factory = new GitHubEventFactory();

        $request = $this->buildRequest('this_is_invalid_json', ['X-GitHub-Event' => ['push']]);

        $factory->buildFromRequest($request);
    }

    /**
     * @expectedException \Swop\GitHubWebHook\Exception\MissingGitHubEventTypeException
     */
    public function testBuildFromRequestShouldFailIfEventTypeIsNotPresentInHeaders()
    {
        $factory = new GitHubEventFactory();

        $request = $this->buildRequest('{}', []);

        $factory->buildFromRequest($request);
    }

    public function testBuildFromRequest()
    {
        $factory = new GitHubEventFactory();

        $request = $this->buildRequest('{"key": "value"}', ['X-GitHub-Event' => ['push']]);

        $event = $factory->buildFromRequest($request);

        $this->assertEquals(new GitHubEvent('push', ['key' => 'value']), $event);
    }

    public function buildDataProvider()
    {
        return [
            ['event_type', ['event_payload'], new GitHubEvent('event_type', ['event_payload'])],
        ];
    }

    /**
     * @param string $body
     * @param array  $headers
     *
     * @return RequestInterface
     */
    private function buildRequest($body, array $headers)
    {
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($body);

        $request = (new ServerRequest())
            ->withBody($stream)
        ;

        foreach ($headers as $headerName => $headerValue) {
            $request = $request->withHeader($headerName, $headerValue);
        }

        return $request;
    }
}
