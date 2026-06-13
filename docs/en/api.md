# API Reference

## Contracts (`Momo\Notifications\Contracts`)

### `NotificationInterface`
- `via(NotifiableInterface $n): list<non-empty-string>` — target channel names.

### `NotifiableInterface`
- `notificationKey(): non-empty-string` — recipient identity / inbox key.
- `routeNotificationFor(non-empty-string $channel): ?non-empty-string` — address per channel.
- `preferredLocale(): ?non-empty-string`.

### `NotificationChannelInterface`
- `name(): non-empty-string`.
- `send(NotifiableInterface $n, NotificationInterface $notification): bool` — true when delivered, false when not applicable; throws on failure.

### `NotificationDispatcherInterface`
- `send(...)` — enqueue for async delivery.
- `sendNow(...)` — deliver synchronously across all `via()` channels.

### `ChannelRegistryInterface`
- `register(NotificationChannelInterface $c)`, `has(name): bool`, `get(name): NotificationChannelInterface` (throws `ChannelNotFoundException`).

### `ThrottlerInterface`
- `allow(non-empty-string $key, positive-int $maxPerWindow, positive-int $windowSeconds): bool`.

### `ShouldThrottleInterface` (opt-in on a notification)
- `throttleKey(NotifiableInterface): non-empty-string`, `throttleMaxPerWindow(): positive-int`, `throttleWindowSeconds(): positive-int`.

### `ClockInterface`
- `now(): int<0, max>`.

## Message builders & value objects (`Momo\Notifications\Messages`)

A notification implements the builder for each channel it supports:

| Builder             | Method                                            | Returns            |
|---------------------|---------------------------------------------------|--------------------|
| `RoutesToMail`      | `toMail(NotifiableInterface): MailMessage`        | subject, body, fromAddress?, locale? |
| `RoutesToSms`       | `toSms(NotifiableInterface): SmsMessage`          | text, sender?      |
| `RoutesToPush`      | `toPush(NotifiableInterface): PushMessage`        | title, body, data  |
| `RoutesToBroadcast` | `toBroadcast(NotifiableInterface): BroadcastMessage` | type, payload   |
| `RoutesToSlack`     | `toSlack(NotifiableInterface): SlackMessage`      | text, channel?, username? |
| `RoutesToWebhook`   | `toWebhook(NotifiableInterface): WebhookMessage`  | event, jsonBody, headers |

All message classes are `final readonly`.

## Channels (`Momo\Notifications\Channels`)
`MailChannel`, `SmsChannel`, `PushChannel`, `BroadcastChannel`, `SlackChannel`,
`WebhookChannel`. Each takes its transport (or the inbox store) and implements
`NotificationChannelInterface`. `SlackChannel` reuses the webhook transport with
a Slack-shaped JSON body.

## Transports (`Momo\Notifications\Transport`)
- `MailTransportInterface` / `ArrayMailTransport`
- `SmsTransportInterface` / `ArraySmsTransport`
- `PushTransportInterface` / `ArrayPushTransport`
- `WebhookTransportInterface` / `ArrayWebhookTransport`
- `NotificationStoreInterface` / `ArrayNotificationStore` (in-app inbox):
  `store()`, `forNotifiable()`, `markAsRead()`, `unreadCount()`; `StoredNotification` VO.

Each `Array*Transport` exposes a `sent()` accessor for assertions.

## Dispatcher (`Momo\Notifications\Dispatcher`)
- `NotificationDispatcher` — implements the dispatcher contract.
- `ChannelRegistry` — implements the registry contract.
- `SendNotificationJob` — `Momo\Queue\Contracts\JobInterface`; runs `sendNow()` on a worker (`maxAttempts() = 3`).

## Throttle / Support
- `Throttle\InMemoryThrottler` — sliding-window limiter.
- `Support\Notifiable` — default `NotifiableInterface` trait (`abstract notificationKey()`, overridable `notificationRoutes()`).
- `Support\SystemClock` / `Support\MutableClock`.

## Events (`Momo\Notifications\Events`)
`NotificationSent`, `NotificationThrottled`, `NotificationFailed` — extend
`Momo\Events\DomainEvent`; carry channel, `notifiableKey`, notification class
(and the `\Throwable` for `NotificationFailed`).
