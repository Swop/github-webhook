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
 * Generic exception which can be thrown during GitHub web hook handling
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
abstract class GitHubWebHookException extends \Exception
{
    /** @var RequestInterface */
    private $request;

    public function __construct(RequestInterface $request, string $message, ?\Throwable $previous = null)
    {
        $this->request = $request;

        parent::__construct($message, 0, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the message to display in non-debug environments
     */
    public function getPublicMessage(): string
    {
        return $this->getMessage();
    }
}
