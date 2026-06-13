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

namespace Momo\Notifications\Contracts;

/**
 * A delivery channel. Implementations are registered in the
 * {@see ChannelRegistryInterface} under their {@see name()} and selected by a
 * notification's {@see NotificationInterface::via()}.
 */
interface NotificationChannelInterface
{
    /**
     * Channel key as referenced by `via()` and `routeNotificationFor()`.
     *
     * @return non-empty-string
     */
    public function name(): string;

    /**
     * Deliver the notification to the recipient over this channel.
     *
     * Returns true when a message was actually dispatched, and false when the
     * channel did not apply — the notification does not implement this channel's
     * message builder, or the recipient has no address for it. Throws on a
     * genuine delivery failure so the queue can retry.
     *
     * @throws \Throwable on an unrecoverable delivery failure
     */
    public function send(NotifiableInterface $notifiable, NotificationInterface $notification): bool;
}
