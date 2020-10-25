<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\GitHubWebHook\Tests\Security;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Swop\GitHubWebHook\Exception\InvalidGitHubRequestSignatureException;
use Swop\GitHubWebHook\Security\SignatureValidator;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Stream;

/**
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class SignatureValidatorTest extends TestCase
{
    const SECRET = 'MyDirtySecret';

    /**
     * @dataProvider correctSignatures
     */
    public function testCorrectSignature(string $requestBody, string $signature)
    {
        $valid = true;

        try {
            (new SignatureValidator())->validate($this->createRequest($requestBody, $signature), self::SECRET);
        } catch (InvalidGitHubRequestSignatureException $e) {
            $valid = false;
        }

        $this->assertTrue($valid);
    }

    /**
     * @dataProvider incorrectSignatures
     */
    public function testIncorrectSignature(string $requestBody, ?string $signature)
    {
        $this->expectException(InvalidGitHubRequestSignatureException::class);

        (new SignatureValidator())->validate($this->createRequest($requestBody, $signature), self::SECRET);
    }

    public function correctSignatures(): array
    {
        return [
            [
                '{"foo": "bar"}',
                $this->createSignature('{"foo": "bar"}')
            ],
            [
                '{"foo": "bar"}',
                $this->createSignature('{"foo": "bar"}', SignatureValidatorTest::SECRET, 'md5')
            ],
            [
                '{"foo": "bar", "baz": true}',
                $this->createSignature('{"foo": "bar", "baz": true}', SignatureValidatorTest::SECRET, 'sha1')
            ],
            [
                '{"foo": "bar", "baz": true}',
                $this->createSignature('{"foo": "bar", "baz": true}', SignatureValidatorTest::SECRET, 'sha256')
            ],
        ];
    }

    public function incorrectSignatures(): array
    {
        return [
            [
                '{"foo": "bar"}',
                'sha1=WrongHashOrInvalidSecret'
            ],
            [
                '{"foo": "bar"}',
                null // No HTTP_X_Hub_Signature header
            ],
            [
                '{"foo": "bar"}',
                'Invalid Signature Header'
            ],
            [
                '{"foo": "bar"}',
                'sha1=' // No hash value
            ],
            [
                '{"foo": "bar"}',
                '=hash' // No algorithm
            ],
            [
                '{"foo": "bar"}',
                '=' // No algo nor hash
            ],
        ];
    }

    private function createSignature(string $signedContent, string $secret = SignatureValidatorTest::SECRET, string $algo = 'sha1'): string
    {
        return sprintf('%s=%s', $algo, hash_hmac($algo, $signedContent, $secret));
    }

    private function createRequest(string $requestContent, ?string $requestSignature): RequestInterface
    {
        if (null === $requestSignature) {
            $requestSignatureHeader = [];
        } else {
            $requestSignatureHeader = [$requestSignature];
        }

        $stream = new Stream('php://temp', 'wb+');
        $stream->write($requestContent);

        $request = (new Request())
            ->withBody($stream);

        if (!empty($requestSignatureHeader)) {
            $request = $request->withAddedHeader('X-Hub-Signature-256', $requestSignatureHeader);
        }

        return $request;
    }
}
