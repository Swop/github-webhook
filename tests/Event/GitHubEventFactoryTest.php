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

use Laminas\Diactoros\Request;
use Laminas\Diactoros\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Swop\GitHubWebHook\Event\GitHubEvent;
use Swop\GitHubWebHook\Event\GitHubEventFactory;
use Swop\GitHubWebHook\Exception\InvalidGitHubRequestPayloadException;
use Swop\GitHubWebHook\Exception\MissingGitHubEventTypeException;

/**
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class GitHubEventFactoryTest extends TestCase
{
    /**
     * @dataProvider buildDataProvider
     */
    public function testBuild(string $type, array $payload, GitHubEvent $expectedEvent)
    {
        $factory = new GitHubEventFactory();

        $this->assertEquals($expectedEvent, $factory->build($type, $payload));
    }

    public function testBuildFromRequestShouldFailIfPayloadIsNotJSON()
    {
        $factory = new GitHubEventFactory();

        $request = $this->buildRequest('this_is_invalid_json', ['X-GitHub-Event' => ['push']]);

        $this->expectException(InvalidGitHubRequestPayloadException::class);

        $factory->buildFromRequest($request);
    }

    public function testBuildFromRequestShouldFailIfEventTypeIsNotPresentInHeaders()
    {
        $factory = new GitHubEventFactory();

        $request = $this->buildRequest('{}', []);

        $this->expectException(MissingGitHubEventTypeException::class);

        $factory->buildFromRequest($request);
    }

    public function testBuildFromRequest()
    {
        $factory = new GitHubEventFactory();

        $request = $this->buildRequest('{"key": "value"}', ['X-GitHub-Event' => ['push']]);

        $event = $factory->buildFromRequest($request);

        $this->assertEquals(new GitHubEvent('push', ['key' => 'value']), $event);
    }

    public function buildDataProvider(): array
    {
        return [
            ['event_type', ['event_payload'], new GitHubEvent('event_type', ['event_payload'])],
        ];
    }

    private function buildRequest(string $body, array $headers): RequestInterface
    {
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($body);

        $request = (new Request())
            ->withBody($stream)
        ;

        foreach ($headers as $headerName => $headerValue) {
            $request = $request->withHeader($headerName, $headerValue);
        }

        return $request;
    }
}
