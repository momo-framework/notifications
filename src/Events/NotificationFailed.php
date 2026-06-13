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

namespace Momo\Notifications\Events;

use Momo\Events\DomainEvent;
use Throwable;

/**
 * Emitted when a channel raised an error while delivering a notification.
 */
final class NotificationFailed extends DomainEvent
{
    /**
     * @param non-empty-string                                              $channel
     * @param non-empty-string                                              $notifiableKey
     * @param class-string<\Momo\Notifications\Contracts\NotificationInterface> $notification
     */
    public function __construct(
        public readonly string $channel,
        public readonly string $notifiableKey,
        public readonly string $notification,
        public readonly Throwable $exception,
    ) {
        parent::__construct();
    }
}
