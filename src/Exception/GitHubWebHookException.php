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

    /**
     * @param RequestInterface $request
     * @param string           $message
     * @param \Exception       $previous
     */
    public function __construct(RequestInterface $request, $message, \Exception $previous = null)
    {
        $this->request = $request;

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the message to display in non-debug environments
     *
     * @return string
     */
    public function getPublicMessage()
    {
        return $this->getMessage();
    }
}
