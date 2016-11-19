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

use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\ServerRequest;
use Swop\GitHub\WebHookSecurityChecker\Fixtures\StringStream;

/**
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class SecurityCheckerTest extends \PHPUnit_Framework_TestCase
{
    const SECRET = 'MyDirtySecret';

    /** @var SecurityChecker */
    private $securityChecker;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->securityChecker = new SecurityChecker(self::SECRET);
    }

    /**
     * @dataProvider correctSignatures
     *
     * @param string $requestBody
     * @param string $signature
     */
    public function testCorrectSignature($requestBody, $signature)
    {
        $this->assertTrue($this->securityChecker->check($this->createRequest($requestBody, $signature)));
    }

    /**
     * @dataProvider incorrectSignatures
     *
     * @param string $requestBody
     * @param string $signature
     */
    public function testIncorrectSignature($requestBody, $signature)
    {
        $this->assertFalse($this->securityChecker->check($this->createRequest($requestBody, $signature)));
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
                $this->createSignature('{"foo": "bar"}', SecurityCheckerTest::SECRET, 'md5')
            ],
            [
                '{"foo": "bar", "baz": true}',
                $this->createSignature('{"foo": "bar", "baz": true}', SecurityCheckerTest::SECRET, 'sha256')
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

        return (new ServerRequest())
            ->withAddedHeader('X-Hub-Signature', $requestSignatureHeader)
            ->withBody(new StringStream($requestContent));
    }

    /**
     * @param string $algo
     * @param string $signedContent
     * @param string $secret
     *
     * @return string
     */
    private function createSignature($signedContent, $secret = SecurityCheckerTest::SECRET, $algo = 'sha1')
    {
        return sprintf('%s=%s', $algo, hash_hmac($algo, $signedContent, $secret));
    }
}
