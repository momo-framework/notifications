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

namespace Momo\Notifications\Channels;

use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Contracts\NotificationChannelInterface;
use Momo\Notifications\Contracts\NotificationInterface;
use Momo\Notifications\Messages\RoutesToBroadcast;
use Momo\Notifications\Transport\NotificationStoreInterface;

/**
 * In-app channel: stores notifications implementing {@see RoutesToBroadcast} in
 * the recipient's inbox, keyed by {@see NotifiableInterface::notificationKey()}.
 */
final class BroadcastChannel implements NotificationChannelInterface
{
    public function __construct(
        private readonly NotificationStoreInterface $store,
    ) {
    }

    public function name(): string
    {
        return 'broadcast';
    }

    public function send(NotifiableInterface $notifiable, NotificationInterface $notification): bool
    {
        if (! $notification instanceof RoutesToBroadcast) {
            return false;
        }

        $this->store->store($notifiable->notificationKey(), $notification->toBroadcast($notifiable));

        return true;
    }
}
