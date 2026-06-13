# Configuration

Defaults live in `config/notifications.php`, merged under the `notifications`
key. Publish and override them in your application's `config/notifications.php`.

| Key                       | Type                              | Default                    | Meaning                                          |
|---------------------------|-----------------------------------|----------------------------|--------------------------------------------------|
| `channels`                | `list<non-empty-string>`          | all six                    | Channel names available to notifications.        |
| `from.mail`               | `non-empty-string`                | `no-reply@example.com`     | Default mail sender when a message sets none.    |
| `from.sms`                | `non-empty-string`                | `MOMO`                     | Default SMS sender id.                            |
| `throttle.max_per_window` | `positive-int`                    | `5`                        | Informational default for app-defined throttles. |
| `throttle.window_seconds` | `positive-int`                    | `3600`                     | Informational default window.                     |

> Throttle limits are read off each notification's `ShouldThrottleInterface`
> methods; the config values are convenience defaults for your own notification
> classes to reference.

## Swappable bindings

Every binding is interface-keyed. Rebind in an application provider to use real
providers instead of the in-memory defaults:

| Interface                          | Default                    |
|------------------------------------|----------------------------|
| `NotificationDispatcherInterface`  | `NotificationDispatcher`   |
| `ChannelRegistryInterface`         | `ChannelRegistry` (6 channels) |
| `ThrottlerInterface`               | `InMemoryThrottler`        |
| `ClockInterface`                   | `SystemClock`              |
| `MailTransportInterface`           | `ArrayMailTransport`       |
| `SmsTransportInterface`            | `ArraySmsTransport`        |
| `PushTransportInterface`           | `ArrayPushTransport`       |
| `WebhookTransportInterface`        | `ArrayWebhookTransport`    |
| `NotificationStoreInterface`       | `ArrayNotificationStore`   |

### Adding a custom channel

```php
$registry->register(new MyChannel(/* ... */));
```

A channel needs a unique `name()`, a matching `RoutesTo*`-style builder
interface, and to return `true`/`false` from `send()` to report delivery.

### Production transports

Bind, for example, an SMTP `MailTransportInterface`, a Twilio
`SmsTransportInterface`, an FCM `PushTransportInterface`, a coroutine-HTTP
`WebhookTransportInterface`, and a database-backed `NotificationStoreInterface`
for a durable in-app inbox.
