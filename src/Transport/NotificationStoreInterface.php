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

use Momo\Notifications\Messages\BroadcastMessage;

/**
 * Inbox storage for the in-app `broadcast` channel. Bind a database-backed
 * implementation for durability; the default {@see ArrayNotificationStore} is
 * process-local.
 */
interface NotificationStoreInterface
{
    /**
     * Persist a message for a recipient and return its id.
     *
     * @param non-empty-string $notifiableKey
     * @return non-empty-string
     */
    public function store(string $notifiableKey, BroadcastMessage $message): string;

    /**
     * A recipient's stored notifications, most recent first.
     *
     * @param non-empty-string $notifiableKey
     * @return list<StoredNotification>
     */
    public function forNotifiable(string $notifiableKey): array;

    /**
     * Mark one notification read. Returns false when the id is unknown.
     *
     * @param non-empty-string $id
     */
    public function markAsRead(string $id): bool;

    /**
     * Number of unread notifications for a recipient.
     *
     * @param non-empty-string $notifiableKey
     * @return int<0, max>
     */
    public function unreadCount(string $notifiableKey): int;
}
