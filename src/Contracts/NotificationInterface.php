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
 * A notification: a message that can be delivered to a recipient over one or
 * more channels.
 *
 * A concrete notification declares the channels it targets via {@see via()} and
 * implements the per-channel message builders it supports — `RoutesToMail`,
 * `RoutesToSms`, `RoutesToBroadcast`, etc. (see the `Messages` namespace). A
 * channel skips a notification that does not implement its builder.
 */
interface NotificationInterface
{
    /**
     * Channel names this notification should be delivered over for the given
     * recipient. Each name must match a channel registered in the
     * {@see ChannelRegistryInterface} (e.g. `mail`, `sms`, `push`, `broadcast`,
     * `slack`, `webhook`).
     *
     * @return list<non-empty-string>
     */
    public function via(NotifiableInterface $notifiable): array;
}
