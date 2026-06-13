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

namespace Momo\Notifications\Providers;

use Momo\Events\Contracts\EventBusInterface;
use Momo\Kernel\Support\ServiceProvider;
use Momo\Notifications\Channels\BroadcastChannel;
use Momo\Notifications\Channels\MailChannel;
use Momo\Notifications\Channels\PushChannel;
use Momo\Notifications\Channels\SlackChannel;
use Momo\Notifications\Channels\SmsChannel;
use Momo\Notifications\Channels\WebhookChannel;
use Momo\Notifications\Contracts\ChannelRegistryInterface;
use Momo\Notifications\Contracts\ClockInterface;
use Momo\Notifications\Contracts\NotificationDispatcherInterface;
use Momo\Notifications\Contracts\ThrottlerInterface;
use Momo\Notifications\Dispatcher\ChannelRegistry;
use Momo\Notifications\Dispatcher\NotificationDispatcher;
use Momo\Notifications\Support\SystemClock;
use Momo\Notifications\Throttle\InMemoryThrottler;
use Momo\Notifications\Transport\ArrayMailTransport;
use Momo\Notifications\Transport\ArrayNotificationStore;
use Momo\Notifications\Transport\ArrayPushTransport;
use Momo\Notifications\Transport\ArraySmsTransport;
use Momo\Notifications\Transport\ArrayWebhookTransport;
use Momo\Notifications\Transport\MailTransportInterface;
use Momo\Notifications\Transport\NotificationStoreInterface;
use Momo\Notifications\Transport\PushTransportInterface;
use Momo\Notifications\Transport\SmsTransportInterface;
use Momo\Notifications\Transport\WebhookTransportInterface;
use Momo\Queue\Contracts\QueueInterface;

/**
 * Wires the notification system into the application container. Transports
 * default to the in-memory implementations; rebind them to real providers.
 *
 * @codeCoverageIgnore
 */
final class NotificationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig(__DIR__ . '/../../config/notifications.php', 'notifications');

        $this->singleton(ClockInterface::class, static fn (): ClockInterface => new SystemClock());

        $this->singleton(MailTransportInterface::class, static fn (): MailTransportInterface => new ArrayMailTransport());
        $this->singleton(SmsTransportInterface::class, static fn (): SmsTransportInterface => new ArraySmsTransport());
        $this->singleton(PushTransportInterface::class, static fn (): PushTransportInterface => new ArrayPushTransport());
        $this->singleton(WebhookTransportInterface::class, static fn (): WebhookTransportInterface => new ArrayWebhookTransport());

        $this->singleton(
            NotificationStoreInterface::class,
            fn (): NotificationStoreInterface => new ArrayNotificationStore($this->clock()),
        );

        $this->singleton(
            ThrottlerInterface::class,
            fn (): ThrottlerInterface => new InMemoryThrottler($this->clock()),
        );

        $this->singleton(
            ChannelRegistryInterface::class,
            fn (): ChannelRegistryInterface => $this->buildRegistry(),
        );

        $this->singleton(
            NotificationDispatcherInterface::class,
            fn (): NotificationDispatcherInterface => $this->buildDispatcher(),
        );
    }

    private function buildRegistry(): ChannelRegistryInterface
    {
        /** @var MailTransportInterface $mail */
        $mail = $this->app->make(MailTransportInterface::class);
        /** @var SmsTransportInterface $sms */
        $sms = $this->app->make(SmsTransportInterface::class);
        /** @var PushTransportInterface $push */
        $push = $this->app->make(PushTransportInterface::class);
        /** @var WebhookTransportInterface $webhook */
        $webhook = $this->app->make(WebhookTransportInterface::class);
        /** @var NotificationStoreInterface $store */
        $store = $this->app->make(NotificationStoreInterface::class);

        return new ChannelRegistry([
            new MailChannel($mail),
            new SmsChannel($sms),
            new PushChannel($push),
            new BroadcastChannel($store),
            new SlackChannel($webhook),
            new WebhookChannel($webhook),
        ]);
    }

    private function buildDispatcher(): NotificationDispatcherInterface
    {
        /** @var ChannelRegistryInterface $channels */
        $channels = $this->app->make(ChannelRegistryInterface::class);
        /** @var ThrottlerInterface $throttler */
        $throttler = $this->app->make(ThrottlerInterface::class);
        /** @var EventBusInterface $events */
        $events = $this->app->make(EventBusInterface::class);
        /** @var QueueInterface $queue */
        $queue = $this->app->make(QueueInterface::class);

        return new NotificationDispatcher($channels, $throttler, $events, $queue);
    }

    private function clock(): ClockInterface
    {
        /** @var ClockInterface $clock */
        $clock = $this->app->make(ClockInterface::class);

        return $clock;
    }
}
