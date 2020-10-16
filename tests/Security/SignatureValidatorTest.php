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

use Psr\Http\Message\RequestInterface;
use Swop\GitHubWebHook\Security\SignatureValidator;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;

/**
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class SignatureValidatorTest extends \PHPUnit_Framework_TestCase
{
    const SECRET = 'MyDirtySecret';

    /**
     * @dataProvider correctSignatures
     *
     * @param string $requestBody
     * @param string $signature
     */
    public function testCorrectSignature($requestBody, $signature)
    {
        (new SignatureValidator())->validate($this->createRequest($requestBody, $signature), self::SECRET);
    }

    /**
     * @dataProvider incorrectSignatures
     * @expectedException \Swop\GitHubWebHook\Exception\InvalidGitHubRequestSignatureException
     *
     * @param string $requestBody
     * @param string $signature
     */
    public function testIncorrectSignature($requestBody, $signature)
    {
        (new SignatureValidator())->validate($this->createRequest($requestBody, $signature), self::SECRET);
    }

    public function correctSignatures()
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
                $this->createSignature('{"foo": "bar", "baz": true}', SignatureValidatorTest::SECRET, 'sha256')
            ],
        ];
    }

    public function incorrectSignatures()
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

    /**
     * @param string $requestContent
     * @param string $requestSignature
     *
     * @return RequestInterface
     */
    private function createRequest($requestContent, $requestSignature)
    {
        if (null === $requestSignature) {
            $requestSignatureHeader = [];
        } else {
            $requestSignatureHeader = [$requestSignature];
        }

        $stream = new Stream('php://temp', 'wb+');
        $stream->write($requestContent);

        return (new ServerRequest())
            ->withAddedHeader('X-Hub-Signature-256', $requestSignatureHeader)
            ->withBody($stream);
    }

    /**
     * @param string $algo
     * @param string $signedContent
     * @param string $secret
     *
     * @return string
     */
    private function createSignature($signedContent, $secret = SignatureValidatorTest::SECRET, $algo = 'sha1')
    {
        return sprintf('%s=%s', $algo, hash_hmac($algo, $signedContent, $secret));
    }
}
