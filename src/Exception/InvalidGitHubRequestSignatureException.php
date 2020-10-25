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
 * Thrown when the incoming GitHub web hook request doesn't have proper signature
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class InvalidGitHubRequestSignatureException extends GitHubWebHookException
{
    /** @var ?string */
    private $signature;

    public function __construct(RequestInterface $request, ?string $signature, ?\Throwable $previous = null)
    {
        $this->signature = $signature;

        parent::__construct($request, 'Invalid GitHub request signature.', $previous);
    }

    public function getSignature(): string
    {
        return $this->signature;
    }
}
