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
 * Signature validator implementation
 *
 * @see \Swop\GitHubWebHook\Security\SignatureValidatorInterface
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class SignatureValidator implements SignatureValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(RequestInterface $request, string $secret): void
    {
        $signature = $this->getSignatureFromHeader($request);
        $requestBody = $request->getBody();
        $requestBody->rewind();

        $payload = $requestBody->getContents();

        if (!$this->validateSignature($signature, $payload, $secret)) {
            throw new InvalidGitHubRequestSignatureException($request, $signature);
        }
    }

    private function getSignatureFromHeader(RequestInterface $request): ?string
    {
        $headerValues = $request->getHeader('X-Hub-Signature-256');

        return empty($headerValues) ? null : current($headerValues);
    }

    private function validateSignature(?string $signature, string $payload, string $secret): bool
    {
        if (null === $signature) {
            return false;
        }

        $explodeResult = explode('=', $signature, 2);

        if (2 !== count($explodeResult)) {
            return false;
        }

        list($algorithm, $hash) = $explodeResult;

        if (empty($algorithm) || empty($hash)) {
            return false;
        }

        if (!in_array($algorithm, hash_algos())) {
            return false;
        }

        $payloadHash = hash_hmac($algorithm, $payload, $secret);

        return $hash === $payloadHash;
    }
}
