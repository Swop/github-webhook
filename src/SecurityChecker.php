<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\GitHub\WebHookSecurityChecker;

use Psr\Http\Message\ServerRequestInterface;

/**
 * This class will check if a given PSR-7 request coming from GitHub in a web hook context
 * contains proper signature based on the provided secret.
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class SecurityChecker
{
    /** @var string */
    private $gitHubWebHookSecret;

    /**
     * @param string $gitHubWebHookSecret GitHub web hook secret
     */
    public function __construct($gitHubWebHookSecret)
    {
        $this->gitHubWebHookSecret = $gitHubWebHookSecret;
    }

    /**
     * Checks is the request contains valid signature
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function check(ServerRequestInterface $request)
    {
        $hubSignature = $request->getHeader('HTTP_X_Hub_Signature');

        if (empty($hubSignature)) {
            return false;
        }

        $hubSignature = current($hubSignature);

        $explodeResult = explode('=', $hubSignature, 2);

        if (2 !== count($explodeResult)) {
            return false;
        }

        list($algorithm, $hash) = $explodeResult;

        if (empty($algorithm) || empty($hash)) {
            return false;
        }

        $requestBody = $request->getBody();
        $requestBody->rewind();
        $payload = $requestBody->getContents();

        $payloadHash = @hash_hmac($algorithm, $payload, $this->gitHubWebHookSecret);

        return $hash === $payloadHash;
    }
}
