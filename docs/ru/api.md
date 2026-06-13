# Справочник API

## Контракты (`Momo\Notifications\Contracts`)

### `NotificationInterface`
- `via(NotifiableInterface $n): list<non-empty-string>` — имена целевых каналов.

### `NotifiableInterface`
- `notificationKey(): non-empty-string` — идентичность получателя / ключ inbox.
- `routeNotificationFor(non-empty-string $channel): ?non-empty-string` — адрес по каналу.
- `preferredLocale(): ?non-empty-string`.

### `NotificationChannelInterface`
- `name(): non-empty-string`.
- `send(NotifiableInterface $n, NotificationInterface $notification): bool` — true при доставке, false если неприменимо; бросает при ошибке.

### `NotificationDispatcherInterface`
- `send(...)` — поставить в очередь для async-доставки.
- `sendNow(...)` — доставить синхронно по всем каналам из `via()`.

### `ChannelRegistryInterface`
- `register(NotificationChannelInterface $c)`, `has(name): bool`, `get(name): NotificationChannelInterface` (бросает `ChannelNotFoundException`).

### `ThrottlerInterface`
- `allow(non-empty-string $key, positive-int $maxPerWindow, positive-int $windowSeconds): bool`.

### `ShouldThrottleInterface` (opt-in на нотификации)
- `throttleKey(NotifiableInterface): non-empty-string`, `throttleMaxPerWindow(): positive-int`, `throttleWindowSeconds(): positive-int`.

### `ClockInterface`
- `now(): int<0, max>`.

## Билдеры и value-объекты (`Momo\Notifications\Messages`)

Нотификация реализует билдер для каждого поддерживаемого канала:

| Билдер              | Метод                                             | Возвращает         |
|---------------------|---------------------------------------------------|--------------------|
| `RoutesToMail`      | `toMail(NotifiableInterface): MailMessage`        | subject, body, fromAddress?, locale? |
| `RoutesToSms`       | `toSms(NotifiableInterface): SmsMessage`          | text, sender?      |
| `RoutesToPush`      | `toPush(NotifiableInterface): PushMessage`        | title, body, data  |
| `RoutesToBroadcast` | `toBroadcast(NotifiableInterface): BroadcastMessage` | type, payload   |
| `RoutesToSlack`     | `toSlack(NotifiableInterface): SlackMessage`      | text, channel?, username? |
| `RoutesToWebhook`   | `toWebhook(NotifiableInterface): WebhookMessage`  | event, jsonBody, headers |

Все классы сообщений — `final readonly`.

## Каналы (`Momo\Notifications\Channels`)
`MailChannel`, `SmsChannel`, `PushChannel`, `BroadcastChannel`, `SlackChannel`,
`WebhookChannel`. Каждый принимает свой транспорт (или inbox-store) и реализует
`NotificationChannelInterface`. `SlackChannel` переиспользует webhook-транспорт с
JSON-телом в формате Slack.

## Транспорты (`Momo\Notifications\Transport`)
- `MailTransportInterface` / `ArrayMailTransport`
- `SmsTransportInterface` / `ArraySmsTransport`
- `PushTransportInterface` / `ArrayPushTransport`
- `WebhookTransportInterface` / `ArrayWebhookTransport`
- `NotificationStoreInterface` / `ArrayNotificationStore` (in-app inbox):
  `store()`, `forNotifiable()`, `markAsRead()`, `unreadCount()`; VO `StoredNotification`.

Каждый `Array*Transport` отдаёт `sent()` для проверок в тестах.

## Диспетчер (`Momo\Notifications\Dispatcher`)
- `NotificationDispatcher` — реализует контракт диспетчера.
- `ChannelRegistry` — реализует контракт реестра.
- `SendNotificationJob` — `Momo\Queue\Contracts\JobInterface`; выполняет `sendNow()` на воркере (`maxAttempts() = 3`).

## Throttle / Support
- `Throttle\InMemoryThrottler` — лимитер со скользящим окном.
- `Support\Notifiable` — дефолтный trait `NotifiableInterface` (`abstract notificationKey()`, переопределяемый `notificationRoutes()`).
- `Support\SystemClock` / `Support\MutableClock`.

## События (`Momo\Notifications\Events`)
`NotificationSent`, `NotificationThrottled`, `NotificationFailed` — расширяют
`Momo\Events\DomainEvent`; несут канал, `notifiableKey`, класс нотификации
(и `\Throwable` для `NotificationFailed`).
