<?php

/**
 * Part of Momo Framework.
 *
 * © Momo Framework
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Vahe Sargsyan <w33bvGL>
 * @copyright Momo Framework
 * @license   AGPL-3.0-or-later <https://www.gnu.org/licenses/agpl-3.0.html>
 * @link      https://github.com/momo-framework
 */

declare(strict_types=1);

namespace Momo\Notifications\Tests\Unit;

use Momo\Notifications\Channels\BroadcastChannel;
use Momo\Notifications\Channels\MailChannel;
use Momo\Notifications\Channels\PushChannel;
use Momo\Notifications\Channels\SlackChannel;
use Momo\Notifications\Channels\SmsChannel;
use Momo\Notifications\Channels\WebhookChannel;
use Momo\Notifications\Contracts\NotificationChannelInterface;
use Momo\Notifications\Dispatcher\ChannelRegistry;
use Momo\Notifications\Dispatcher\NotificationDispatcher;
use Momo\Notifications\Dispatcher\SendNotificationJob;
use Momo\Notifications\Events\NotificationFailed;
use Momo\Notifications\Events\NotificationSent;
use Momo\Notifications\Events\NotificationThrottled;
use Momo\Notifications\Support\MutableClock;
use Momo\Notifications\Tests\Fixtures\ExplodingMailTransport;
use Momo\Notifications\Tests\Fixtures\OrderShippedNotification;
use Momo\Notifications\Tests\Fixtures\SpyEventBus;
use Momo\Notifications\Tests\Fixtures\TestUser;
use Momo\Notifications\Tests\Fixtures\ThrottledNotification;
use Momo\Notifications\Throttle\InMemoryThrottler;
use Momo\Notifications\Transport\ArrayMailTransport;
use Momo\Notifications\Transport\ArrayNotificationStore;
use Momo\Notifications\Transport\ArrayPushTransport;
use Momo\Notifications\Transport\ArraySmsTransport;
use Momo\Notifications\Transport\ArrayWebhookTransport;
use Momo\Queue\Queue\InMemoryQueue;
use Momo\Queue\Support\MutableClock as QueueClock;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class NotificationDispatcherTest extends TestCase
{
    private ArrayMailTransport $mail;

    private ArraySmsTransport $sms;

    private ArrayPushTransport $push;

    private ArrayWebhookTransport $webhook;

    private ArrayNotificationStore $store;

    private SpyEventBus $events;

    private InMemoryQueue $queue;

    private MutableClock $clock;

    protected function setUp(): void
    {
        $this->mail    = new ArrayMailTransport();
        $this->sms     = new ArraySmsTransport();
        $this->push    = new ArrayPushTransport();
        $this->webhook = new ArrayWebhookTransport();
        $this->clock   = new MutableClock(0);
        $this->store   = new ArrayNotificationStore($this->clock);
        $this->events  = new SpyEventBus();
        $this->queue   = new InMemoryQueue(new QueueClock(0));
    }

    public function testSendNowDeliversAcrossEveryChannel(): void
    {
        $dispatcher = $this->dispatcher();
        $user       = new TestUser('u-1', [
            'mail'    => 'a@example.com',
            'sms'     => '+10000000000',
            'push'    => 'device-token',
            'slack'   => 'https://hooks.slack.com/x',
            'webhook' => 'https://example.com/hook',
        ]);

        $dispatcher->sendNow($user, new OrderShippedNotification('o-7'));

        self::assertCount(1, $this->mail->sent());
        self::assertCount(1, $this->sms->sent());
        self::assertCount(1, $this->push->sent());
        self::assertCount(2, $this->webhook->sent(), 'slack + webhook both use the webhook transport');
        self::assertCount(1, $this->store->forNotifiable('u-1'));
        self::assertSame(6, $this->events->countOf(NotificationSent::class));
    }

    public function testChannelsWithoutARouteAreSkippedAndEmitNoSentEvent(): void
    {
        $dispatcher = $this->dispatcher();
        $user       = new TestUser('u-2', ['mail' => 'a@example.com']);

        $dispatcher->sendNow($user, new OrderShippedNotification('o-9'));

        self::assertCount(1, $this->mail->sent());
        self::assertCount(0, $this->sms->sent());
        self::assertCount(0, $this->push->sent());
        self::assertCount(0, $this->webhook->sent());
        self::assertCount(1, $this->store->forNotifiable('u-2'), 'broadcast needs no route');
        self::assertSame(2, $this->events->countOf(NotificationSent::class));
    }

    public function testChannelFailureEmitsFailedEventAndRethrows(): void
    {
        $registry   = new ChannelRegistry([new MailChannel(new ExplodingMailTransport())]);
        $dispatcher = new NotificationDispatcher(
            $registry,
            new InMemoryThrottler($this->clock),
            $this->events,
            $this->queue,
        );
        $user = new TestUser('u-3', ['mail' => 'a@example.com']);

        $caught = null;
        try {
            $dispatcher->sendNow($user, new ThrottledNotification());
        } catch (RuntimeException $exception) {
            $caught = $exception;
        }

        self::assertInstanceOf(RuntimeException::class, $caught);
        self::assertSame(1, $this->events->countOf(NotificationFailed::class));
        self::assertSame(0, $this->events->countOf(NotificationSent::class));
    }

    public function testThrottlingSuppressesDeliveryBeyondQuota(): void
    {
        $dispatcher = $this->dispatcher();
        $user       = new TestUser('u-4', ['mail' => 'a@example.com']);

        $dispatcher->sendNow($user, new ThrottledNotification());
        $dispatcher->sendNow($user, new ThrottledNotification());
        $dispatcher->sendNow($user, new ThrottledNotification());

        self::assertCount(2, $this->mail->sent());
        self::assertSame(2, $this->events->countOf(NotificationSent::class));
        self::assertSame(1, $this->events->countOf(NotificationThrottled::class));
    }

    public function testSendEnqueuesAJobForAsyncDelivery(): void
    {
        $dispatcher = $this->dispatcher();
        $user       = new TestUser('u-5', ['mail' => 'a@example.com']);

        $dispatcher->send($user, new OrderShippedNotification('o-1'));

        self::assertSame(1, $this->queue->size());
        self::assertCount(0, $this->mail->sent(), 'nothing delivered until the worker runs');

        $envelope = $this->queue->pop();
        self::assertNotNull($envelope);
        self::assertInstanceOf(SendNotificationJob::class, $envelope->job);

        $envelope->job->handle();

        self::assertCount(1, $this->mail->sent());
    }

    private function dispatcher(): NotificationDispatcher
    {
        return new NotificationDispatcher(
            $this->registry(),
            new InMemoryThrottler($this->clock),
            $this->events,
            $this->queue,
        );
    }

    private function registry(): ChannelRegistry
    {
        /** @var list<NotificationChannelInterface> $channels */
        $channels = [
            new MailChannel($this->mail),
            new SmsChannel($this->sms),
            new PushChannel($this->push),
            new BroadcastChannel($this->store),
            new SlackChannel($this->webhook),
            new WebhookChannel($this->webhook),
        ];

        return new ChannelRegistry($channels);
    }
}
