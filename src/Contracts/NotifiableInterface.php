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
 * A recipient of notifications — typically a User model using the
 * {@see \Momo\Notifications\Support\Notifiable} trait.
 */
interface NotifiableInterface
{
    /**
     * Stable identity of this recipient, used as the in-app inbox key and the
     * default throttling subject. Usually the model's primary key.
     *
     * @return non-empty-string
     */
    public function notificationKey(): string;

    /**
     * Destination address for the given channel — e.g. an email for `mail`, a
     * phone number for `sms`, a device token for `push`, a URL for `webhook`
     * or `slack`. Returns null when the recipient has no address for it.
     *
     * @param non-empty-string $channel
     * @return non-empty-string|null
     */
    public function routeNotificationFor(string $channel): ?string;

    /**
     * Preferred locale for rendering messages, or null to use the app default.
     *
     * @return non-empty-string|null
     */
    public function preferredLocale(): ?string;
}
