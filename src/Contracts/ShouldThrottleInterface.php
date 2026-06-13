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
 * Opt-in contract: a notification that wants its delivery rate-limited per
 * recipient. The dispatcher consults the {@see ThrottlerInterface} before each
 * channel send and skips the send when the quota is exhausted.
 */
interface ShouldThrottleInterface
{
    /**
     * Throttling subject — usually derived from the recipient. The dispatcher
     * additionally namespaces this by channel.
     *
     * @return non-empty-string
     */
    public function throttleKey(NotifiableInterface $notifiable): string;

    /**
     * Maximum deliveries allowed inside one window.
     *
     * @return positive-int
     */
    public function throttleMaxPerWindow(): int;

    /**
     * Window length in seconds.
     *
     * @return positive-int
     */
    public function throttleWindowSeconds(): int;
}
