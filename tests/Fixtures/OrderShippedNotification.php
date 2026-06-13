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

namespace Momo\Notifications\Tests\Fixtures;

use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Contracts\NotificationInterface;
use Momo\Notifications\Messages\BroadcastMessage;
use Momo\Notifications\Messages\MailMessage;
use Momo\Notifications\Messages\PushMessage;
use Momo\Notifications\Messages\RoutesToBroadcast;
use Momo\Notifications\Messages\RoutesToMail;
use Momo\Notifications\Messages\RoutesToPush;
use Momo\Notifications\Messages\RoutesToSlack;
use Momo\Notifications\Messages\RoutesToSms;
use Momo\Notifications\Messages\RoutesToWebhook;
use Momo\Notifications\Messages\SlackMessage;
use Momo\Notifications\Messages\SmsMessage;
use Momo\Notifications\Messages\WebhookMessage;

/**
 * Exercises every channel: declares all six message builders.
 */
final class OrderShippedNotification implements
    NotificationInterface,
    RoutesToMail,
    RoutesToSms,
    RoutesToPush,
    RoutesToBroadcast,
    RoutesToSlack,
    RoutesToWebhook
{
    /**
     * @param non-empty-string $orderId
     */
    public function __construct(private readonly string $orderId)
    {
    }

    public function via(NotifiableInterface $notifiable): array
    {
        return ['mail', 'sms', 'push', 'broadcast', 'slack', 'webhook'];
    }

    public function toMail(NotifiableInterface $notifiable): MailMessage
    {
        return new MailMessage(
            subject: 'Your order has shipped',
            body: 'Order ' . $this->orderId . ' is on its way.',
            locale: $notifiable->preferredLocale(),
        );
    }

    public function toSms(NotifiableInterface $notifiable): SmsMessage
    {
        return new SmsMessage('Order ' . $this->orderId . ' shipped.');
    }

    public function toPush(NotifiableInterface $notifiable): PushMessage
    {
        return new PushMessage('Order shipped', 'Order ' . $this->orderId . ' is on its way.', ['order_id' => $this->orderId]);
    }

    public function toBroadcast(NotifiableInterface $notifiable): BroadcastMessage
    {
        return new BroadcastMessage('order.shipped', ['order_id' => $this->orderId]);
    }

    public function toSlack(NotifiableInterface $notifiable): SlackMessage
    {
        return new SlackMessage('Order ' . $this->orderId . ' shipped.', channel: '#orders');
    }

    public function toWebhook(NotifiableInterface $notifiable): WebhookMessage
    {
        return new WebhookMessage(
            event: 'order.shipped',
            jsonBody: \json_encode(['order_id' => $this->orderId], \JSON_THROW_ON_ERROR),
        );
    }
}
