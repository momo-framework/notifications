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

namespace Momo\Notifications\Transport;

use Momo\Notifications\Contracts\ClockInterface;
use Momo\Notifications\Messages\BroadcastMessage;

/**
 * Process-local in-app inbox. Suitable for a single worker and tests; bind a
 * persistent store for production durability.
 */
final class ArrayNotificationStore implements NotificationStoreInterface
{
    /** @var array<non-empty-string, StoredNotification> insertion-ordered map */
    private array $items = [];

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function store(string $notifiableKey, BroadcastMessage $message): string
    {
        $id = \bin2hex(\random_bytes(16));

        $this->items[$id] = new StoredNotification(
            id: $id,
            notifiableKey: $notifiableKey,
            message: $message,
            createdAt: $this->clock->now(),
        );

        return $id;
    }

    public function forNotifiable(string $notifiableKey): array
    {
        $matching = \array_filter(
            $this->items,
            static fn (StoredNotification $n): bool => $n->notifiableKey === $notifiableKey,
        );

        return \array_reverse(\array_values($matching));
    }

    public function markAsRead(string $id): bool
    {
        if (! isset($this->items[$id])) {
            return false;
        }

        $this->items[$id] = $this->items[$id]->asRead();

        return true;
    }

    public function unreadCount(string $notifiableKey): int
    {
        return \count(\array_filter(
            $this->items,
            static fn (StoredNotification $n): bool => $n->notifiableKey === $notifiableKey && ! $n->read,
        ));
    }
}
