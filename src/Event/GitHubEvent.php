<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <sylvain@mauduit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Swop\GitHubWebHook\Event;

/**
 * Represents a GitHub Event
 *
 * @author Sylvain Mauduit <sylvain@mauduit.fr>
 */
class GitHubEvent
{
    /** @var string */
    private $type;
    /** @var array */
    private $payload;

    public function __construct(string $type, array $payload)
    {
        $this->type    = $type;
        $this->payload = $payload;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
