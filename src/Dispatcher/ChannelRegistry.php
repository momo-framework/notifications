<?php

/**
 * Part of Momo Framework.
 *
 * © Momo Framework
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Unauthorized copying, modification, or distribution of this file,
 * via any medium, is strictly prohibited without prior written permission
 * from the copyright holder.
 *
 * @author    Vahe Sargsyan <w33bvGL>
 * @copyright Momo Framework
 * @license   AGPL-3.0-or-later <https://www.gnu.org/licenses/agpl-3.0.html>
 * @link      https://github.com/momo-framework
 */

declare(strict_types=1);

namespace Momo\Notifications\Dispatcher;

use Momo\Notifications\Contracts\ChannelRegistryInterface;
use Momo\Notifications\Contracts\NotificationChannelInterface;
use Momo\Notifications\Exceptions\ChannelNotFoundException;

/**
 * In-memory registry of delivery channels keyed by {@see NotificationChannelInterface::name()}.
 */
final class ChannelRegistry implements ChannelRegistryInterface
{
    /** @var array<non-empty-string, NotificationChannelInterface> */
    private array $channels = [];

    /**
     * @param iterable<NotificationChannelInterface> $channels
     */
    public function __construct(iterable $channels = [])
    {
        foreach ($channels as $channel) {
            $this->register($channel);
        }
    }

    public function register(NotificationChannelInterface $channel): void
    {
        $this->channels[$channel->name()] = $channel;
    }

    public function has(string $name): bool
    {
        return isset($this->channels[$name]);
    }

    public function get(string $name): NotificationChannelInterface
    {
        return $this->channels[$name] ?? throw ChannelNotFoundException::named($name);
    }
}
