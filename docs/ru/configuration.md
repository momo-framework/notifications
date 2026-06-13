# Конфигурация

Значения по умолчанию в `config/notifications.php`, мёржатся под ключом
`notifications`. Опубликуйте и переопределите их в `config/notifications.php`
приложения.

| Ключ                      | Тип                               | По умолчанию               | Значение                                         |
|---------------------------|-----------------------------------|----------------------------|--------------------------------------------------|
| `channels`                | `list<non-empty-string>`          | все шесть                  | Имена каналов, доступных нотификациям.           |
| `from.mail`               | `non-empty-string`                | `no-reply@example.com`     | Отправитель почты по умолчанию.                  |
| `from.sms`                | `non-empty-string`                | `MOMO`                     | Идентификатор отправителя SMS.                   |
| `throttle.max_per_window` | `positive-int`                    | `5`                        | Справочный дефолт для throttle приложения.       |
| `throttle.window_seconds` | `positive-int`                    | `3600`                     | Справочное окно по умолчанию.                     |

> Лимиты throttle читаются из методов `ShouldThrottleInterface` каждой
> нотификации; значения конфига — удобные дефолты, на которые могут ссылаться
> ваши классы нотификаций.

## Подменяемые биндинги

Все биндинги по интерфейсам. Переопределите в провайдере приложения, чтобы
использовать реальные провайдеры вместо in-memory дефолтов:

| Интерфейс                          | По умолчанию               |
|------------------------------------|----------------------------|
| `NotificationDispatcherInterface`  | `NotificationDispatcher`   |
| `ChannelRegistryInterface`         | `ChannelRegistry` (6 каналов) |
| `ThrottlerInterface`               | `InMemoryThrottler`        |
| `ClockInterface`                   | `SystemClock`              |
| `MailTransportInterface`           | `ArrayMailTransport`       |
| `SmsTransportInterface`            | `ArraySmsTransport`        |
| `PushTransportInterface`           | `ArrayPushTransport`       |
| `WebhookTransportInterface`        | `ArrayWebhookTransport`    |
| `NotificationStoreInterface`       | `ArrayNotificationStore`   |

### Добавление своего канала

```php
$registry->register(new MyChannel(/* ... */));
```

Каналу нужны уникальный `name()`, парный интерфейс-билдер в стиле `RoutesTo*` и
возврат `true`/`false` из `send()` для отчёта о доставке.

### Транспорты для прода

Свяжите, например, SMTP `MailTransportInterface`, Twilio
`SmsTransportInterface`, FCM `PushTransportInterface`, корутинный HTTP
`WebhookTransportInterface` и `NotificationStoreInterface` на БД для устойчивого
in-app inbox.
