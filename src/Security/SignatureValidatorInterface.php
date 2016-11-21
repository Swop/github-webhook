<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\GitHubWebHook\Security;

use Psr\Http\Message\RequestInterface;
use Swop\GitHubWebHook\Exception\InvalidGitHubRequestSignatureException;

/**
 * Class which implements this interface will check if a given PSR-7 request coming from GitHub in a web hook context
 * contains proper signature based on the provided secret.
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
interface SignatureValidatorInterface
{
    /**
     * Checks is the request contains valid signature
     *
     * @param RequestInterface $request Incoming request
     * @param string           $secret  GitHub web hook secret
     *
     * @throws InvalidGitHubRequestSignatureException
     */
    public function validate(RequestInterface $request, $secret);
}
