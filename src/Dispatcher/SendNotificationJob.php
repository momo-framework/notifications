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

use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Contracts\NotificationDispatcherInterface;
use Momo\Notifications\Contracts\NotificationInterface;
use Momo\Queue\Contracts\JobInterface;
use Throwable;

/**
 * Queue job that performs synchronous delivery on a worker.
 *
 * AT-LEAST-ONCE NOTE: a notification with several channels is delivered as one
 * job. If a later channel fails and the job is retried, earlier channels run
 * again — delivery is at-least-once per channel. Make channel sends idempotent,
 * or split per channel, when exactly-once matters.
 */
final class SendNotificationJob implements JobInterface
{
    public function __construct(
        private readonly NotificationDispatcherInterface $dispatcher,
        private readonly NotifiableInterface $notifiable,
        private readonly NotificationInterface $notification,
    ) {
    }

    public function handle(): void
    {
        $this->dispatcher->sendNow($this->notifiable, $this->notification);
    }

    public function failed(Throwable $exception): void
    {
    }

    public function maxAttempts(): int
    {
        return 3;
    }
}
