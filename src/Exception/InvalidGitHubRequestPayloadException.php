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
 * Thrown when the incoming GitHub web hook request contains invalid payload body
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class InvalidGitHubRequestPayloadException extends GitHubWebHookException
{
    /** @var string */
    private $requestBody;

    public function __construct(RequestInterface $request, string $requestBody, ?\Throwable $previous = null)
    {
        $this->requestBody = $requestBody;

        parent::__construct($request, 'Invalid GitHub request payload.', $previous);
    }

    public function getRequestBody(): string
    {
        return $this->requestBody;
    }
}
