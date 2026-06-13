# Overview

`momo/notifications` is the unified outbound-messaging layer of Momo. A single
notification object describes *what* to tell a recipient; the package decides
*how* — fanning it out across mail, SMS, push, an in-app inbox, Slack and
arbitrary webhooks — without the calling code knowing any transport details.

## Flow

```
$dispatcher->send($user, new OrderShipped())
        │
        ▼  push SendNotificationJob
   Momo queue ──▶ worker ──▶ dispatcher->sendNow()
                                   │  for each channel in via():
                                   ├─ throttled?  → NotificationThrottled, skip
                                   ├─ channel->send() delivered? → NotificationSent
                                   └─ threw?       → NotificationFailed, rethrow (queue retries)
```

`send()` enqueues; the queue worker later calls `sendNow()`, which performs the
actual per-channel delivery. `sendNow()` may also be called directly for
synchronous sends (e.g. a verification code that must go out within the request).

## Building blocks

| Concept       | Type                                  | Responsibility                                   |
|---------------|---------------------------------------|--------------------------------------------------|
| Notification  | `NotificationInterface` + `RoutesTo*` | declares target channels and builds each message |
| Recipient     | `NotifiableInterface` / `Notifiable`  | identity, per-channel address, preferred locale  |
| Channel       | `NotificationChannelInterface`        | turns a notification into a transport call       |
| Transport     | `*TransportInterface`                 | the actual delivery mechanism (swap for real providers) |
| Dispatcher    | `NotificationDispatcherInterface`     | routing, throttling, events, async hand-off      |
| Throttler     | `ThrottlerInterface`                  | per-recipient rate limiting                      |
| Inbox         | `NotificationStoreInterface`          | storage for the in-app `broadcast` channel       |

## Design decisions

- **Per-channel builder interfaces, not a fat base class.** A notification
  implements `RoutesToMail`, `RoutesToSms`, … only for the channels it supports.
  A channel uses `instanceof` to decide applicability — fully type-safe, no
  `method_exists` reflection, no `mixed`.
- **`send()` returns a bool.** A channel reports whether it actually delivered
  (vs. skipped for a missing route/builder), so the dispatcher emits an accurate
  `NotificationSent` only on real delivery.
- **Queue-first.** Dispatch defaults to asynchronous via `momo/queue`, keeping
  the request hot path free of SMTP/HTTP latency. See the at-least-once note in
  `SendNotificationJob` for the multi-channel retry semantics.
- **Transports are interfaces with in-memory defaults.** The shipped
  `Array*Transport` implementations are real, tested and useful for local/dev
  and tests; production binds SMTP, Twilio, FCM, a coroutine HTTP client, etc.

## Localization

`NotifiableInterface::preferredLocale()` exposes the recipient's locale; message
builders read it (e.g. `MailMessage::$locale`) to render localized content.
Heavy translation belongs to a dedicated i18n package — this layer only carries
the locale through.

## Coroutine & memory safety

The default throttler and in-app store keep state for the worker's lifetime, not
per request, and their mutating methods contain no I/O suspension point — atomic
with respect to sibling coroutines in one Swoole worker. For cross-worker
correctness (shared throttle counters, durable inbox) bind Redis/database
implementations of `ThrottlerInterface` and `NotificationStoreInterface`.
