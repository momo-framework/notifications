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

use Momo\Events\Contracts\EventBusInterface;
use Momo\Notifications\Contracts\ChannelRegistryInterface;
use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Contracts\NotificationDispatcherInterface;
use Momo\Notifications\Contracts\NotificationInterface;
use Momo\Notifications\Contracts\ShouldThrottleInterface;
use Momo\Notifications\Contracts\ThrottlerInterface;
use Momo\Notifications\Events\NotificationFailed;
use Momo\Notifications\Events\NotificationSent;
use Momo\Notifications\Events\NotificationThrottled;
use Momo\Queue\Contracts\QueueInterface;
use Throwable;

/**
 * Routes notifications to channels, enforcing per-recipient throttling and
 * publishing lifecycle events. {@see send()} enqueues for asynchronous delivery;
 * {@see sendNow()} delivers inline.
 */
final class NotificationDispatcher implements NotificationDispatcherInterface
{
    public function __construct(
        private readonly ChannelRegistryInterface $channels,
        private readonly ThrottlerInterface $throttler,
        private readonly EventBusInterface $events,
        private readonly QueueInterface $queue,
    ) {
    }

    public function send(NotifiableInterface $notifiable, NotificationInterface $notification): void
    {
        $this->queue->push(new SendNotificationJob($this, $notifiable, $notification));
    }

    public function sendNow(NotifiableInterface $notifiable, NotificationInterface $notification): void
    {
        foreach ($notification->via($notifiable) as $channelName) {
            $this->deliver($channelName, $notifiable, $notification);
        }
    }

    /**
     * @param non-empty-string $channelName
     */
    private function deliver(string $channelName, NotifiableInterface $notifiable, NotificationInterface $notification): void
    {
        $key = $notifiable->notificationKey();

        if ($this->isThrottled($channelName, $notifiable, $notification)) {
            $this->events->publish(new NotificationThrottled($channelName, $key, $notification::class));

            return;
        }

        $channel = $this->channels->get($channelName);

        try {
            $delivered = $channel->send($notifiable, $notification);
        } catch (Throwable $exception) {
            $this->events->publish(new NotificationFailed($channelName, $key, $notification::class, $exception));

            throw $exception;
        }

        if ($delivered) {
            $this->events->publish(new NotificationSent($channelName, $key, $notification::class));
        }
    }

    /**
     * @param non-empty-string $channelName
     */
    private function isThrottled(string $channelName, NotifiableInterface $notifiable, NotificationInterface $notification): bool
    {
        if (! $notification instanceof ShouldThrottleInterface) {
            return false;
        }

        $key = $notification->throttleKey($notifiable) . ':' . $channelName;

        return ! $this->throttler->allow(
            $key,
            $notification->throttleMaxPerWindow(),
            $notification->throttleWindowSeconds(),
        );
    }
}
