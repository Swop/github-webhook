<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\GitHubWebHook\Exception;

use Psr\Http\Message\RequestInterface;

/**
 * Thrown when the incoming GitHub web hook request doesn't have event type header
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class MissingGitHubEventTypeException extends GitHubWebHookException
{
    public function __construct(RequestInterface $request, ?\Throwable $previous = null)
    {
        parent::__construct($request, 'A GitHub event type should be provided as a X-GitHub-Event header.', $previous);
    }
}
