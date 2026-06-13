# momo/notifications

Унифицированная многоканальная система нотификаций — email, SMS, push, in-app, Slack и webhook — с асинхронной диспетчеризацией через очередь Momo.

> 🇬🇧 Documentation in English: [README.md](README.md)

## Установка

```bash
composer require momo-framework/notifications
```

`NotificationsServiceProvider` обнаруживается автоматически. Он связывает
`NotificationDispatcherInterface`, шесть каналов, их транспорты (по умолчанию
in-memory), throttler и in-app хранилище.

## Быстрый старт

Сделайте модель получателем:

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

Опишите нотификацию — реализуйте билдер для каждого поддерживаемого канала:

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
            subject: 'Ваш заказ отправлен',
            body: "Заказ {$this->orderId} в пути.",
            locale: $n->preferredLocale(),
        );
    }
}
```

Отправьте:

```php
$dispatcher->send($user, new OrderShipped('o-42'));      // в очередь (async)
$dispatcher->sendNow($user, new OrderShipped('o-42'));   // сразу (sync)
```

## Каналы

| Канал       | Интерфейс-билдер     | Сообщение     | Маршрутизация                   |
|-------------|----------------------|---------------|---------------------------------|
| `mail`      | `RoutesToMail`       | `MailMessage` | `routeNotificationFor('mail')`  |
| `sms`       | `RoutesToSms`        | `SmsMessage`  | `routeNotificationFor('sms')`   |
| `push`      | `RoutesToPush`       | `PushMessage` | `routeNotificationFor('push')`  |
| `broadcast` | `RoutesToBroadcast`  | `BroadcastMessage` | `notificationKey()` (in-app inbox) |
| `slack`     | `RoutesToSlack`      | `SlackMessage`| `routeNotificationFor('slack')` |
| `webhook`   | `RoutesToWebhook`    | `WebhookMessage` | `routeNotificationFor('webhook')` |

Канал пропускает нотификацию, которая не реализует его билдер, либо получателя
без адреса для этого канала.

## Throttling

Реализуйте `ShouldThrottleInterface`, чтобы ограничить число доставок на
получателя в окне; диспетчер сверяется с throttler перед каждой отправкой и
публикует событие `NotificationThrottled`, когда подавляет доставку.

## Документация

- [Обзор](docs/ru/overview.md)
- [Справочник API](docs/ru/api.md)
- [Конфигурация](docs/ru/configuration.md)

## Лицензия

AGPL-3.0-or-later.
