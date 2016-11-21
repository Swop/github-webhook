<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\GitHubWebHook\Event;

use Psr\Http\Message\RequestInterface;
use Swop\GitHubWebHook\Exception\InvalidGitHubRequestPayloadException;
use Swop\GitHubWebHook\Exception\MissingGitHubEventTypeException;

/**
 * Factory implementation which handle the GitHub event creation
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class GitHubEventFactory implements GitHubEventFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function build($type, array $payload)
    {
        return new GitHubEvent($type, $payload);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFromRequest(RequestInterface $request)
    {
        $eventType = $request->getHeader('X-GitHub-Event');

        if (count($eventType) > 0) {
            $eventType = current($eventType);
        } else {
            throw new MissingGitHubEventTypeException($request);
        }

        $body = $request->getBody();
        $body->rewind();
        $body = $body->getContents();

        $payload = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidGitHubRequestPayloadException($request, $body);
        }

        return $this->build($eventType, $payload);
    }
}
