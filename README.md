# momo/notifications

Unified, multi-channel notification system — mail, SMS, push, in-app, Slack and webhook — dispatched asynchronously through the Momo queue.

> 🇷🇺 Документация на русском: [README.ru.md](README.ru.md)

## Installation

```bash
composer require momo-framework/notifications
```

`NotificationsServiceProvider` is auto-discovered. It binds
`NotificationDispatcherInterface`, the six channels, their transports (in-memory
by default), the throttler and the in-app store.

## Quick Start

Make a model notifiable:

```php
use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Support\Notifiable;

final class User implements NotifiableInterface
{
    use Notifiable;

    public function __construct(private string $id, private string $email) {}

    public function notificationKey(): string { return $this->id; }

    protected function notificationRoutes(): array
    {
        return ['mail' => $this->email];
    }
}
```

Write a notification — implement the builder for each channel it supports:

```php
use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Contracts\NotificationInterface;
use Momo\Notifications\Messages\{MailMessage, RoutesToMail};

final class OrderShipped implements NotificationInterface, RoutesToMail
{
    public function __construct(private string $orderId) {}

    public function via(NotifiableInterface $n): array { return ['mail']; }

    public function toMail(NotifiableInterface $n): MailMessage
    {
        return new MailMessage(
            subject: 'Your order shipped',
            body: "Order {$this->orderId} is on its way.",
            locale: $n->preferredLocale(),
        );
    }
}
```

Dispatch it:

```php
$dispatcher->send($user, new OrderShipped('o-42'));      // queued (async)
$dispatcher->sendNow($user, new OrderShipped('o-42'));   // inline (sync)
```

## Channels

| Channel     | Builder interface    | Message       | Routed by                       |
|-------------|----------------------|---------------|---------------------------------|
| `mail`      | `RoutesToMail`       | `MailMessage` | `routeNotificationFor('mail')`  |
| `sms`       | `RoutesToSms`        | `SmsMessage`  | `routeNotificationFor('sms')`   |
| `push`      | `RoutesToPush`       | `PushMessage` | `routeNotificationFor('push')`  |
| `broadcast` | `RoutesToBroadcast`  | `BroadcastMessage` | `notificationKey()` (in-app inbox) |
| `slack`     | `RoutesToSlack`      | `SlackMessage`| `routeNotificationFor('slack')` |
| `webhook`   | `RoutesToWebhook`    | `WebhookMessage` | `routeNotificationFor('webhook')` |

A channel skips a notification that does not implement its builder, or a
recipient with no address for it.

## Throttling

Implement `ShouldThrottleInterface` to cap deliveries per recipient per window;
the dispatcher consults the throttler before each channel send and emits a
`NotificationThrottled` event when it suppresses one.

## Documentation

- [Overview](docs/en/overview.md)
- [API reference](docs/en/api.md)
- [Configuration](docs/en/configuration.md)

## License

AGPL-3.0-or-later.
