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
 * Factory which handle the GitHub event creation
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
interface GitHubEventFactoryInterface
{
    /**
     * Build a GitHub web hook event object depending on the given type/payload
     *
     * @param string $type
     * @param array  $payload
     *
     * @return GitHubEvent
     */
    public function build($type, array $payload);

    /**
     * Build a GitHub web hook event object based on the incoming request
     *
     * @param RequestInterface $request
     *
     * @return GitHubEvent
     *
     * @throws InvalidGitHubRequestPayloadException If the request payload is not a valid JSON content
     * @throws MissingGitHubEventTypeException If the GitHub event type could not be found in the request headers
     */
    public function buildFromRequest(RequestInterface $request);
}
