<?php

/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\GitHub\WebHookSecurityChecker\Fixtures;

use Psr\Http\Message\StreamInterface;

/**
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class StringStream implements StreamInterface
{
    private $content;

    public function __construct($content = '')
    {
        $this->content = $content;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function close()
    {
    }

    public function detach()
    {
    }

    public function getSize()
    {
    }

    public function tell()
    {
        return 0;
    }

    public function eof()
    {
        return true;
    }

    public function isSeekable()
    {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
    }

    public function rewind()
    {
    }

    public function isWritable()
    {
        return false;
    }

    public function write($string)
    {
    }

    public function isReadable()
    {
        return true;
    }

    public function read($length)
    {
        return $this->content;
    }

    public function getContents()
    {
        return $this->content;
    }

    public function getMetadata($key = null)
    {
    }
}
