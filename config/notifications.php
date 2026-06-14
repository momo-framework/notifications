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

return [
    // › Notification Dispatch Channels
    // ─────────────────────────────────────────────────────────────────── ⚛ ───
    //   Registers the white-listed transport topologies available for system
    //   alerts. The framework's NotificationDispatcher intercepts the via()
    //   matrix of outbound payloads and coordinates asynchronous multi-channel
    //   broadcasting. Each delivery vector must map directly to an active,
    //   coroutine-aware driver instance bound inside the ChannelRegistry.
    //
    //   Supported Options:
    //     • "mail"      - Asynchronous SMTP or API-driven email delivery.
    //     • "sms"       - High-throughput cellular short message service gateway.
    //     • "push"      - Real-time WebSocket or HTTP/2 push notification service.
    //     • "broadcast" - Real-time client synchronization via Swoole WebSockets.
    //     • "slack"     - Synchronous or pooled Webhook integration for Slack channels.
    //     • "webhook"   - Non-blocking reactive HTTP POST payloads to external APIs.
    //

    'channels' => ['mail', 'sms', 'push', 'broadcast', 'slack', 'webhook'],

    // › Global Sender Identities
    // ─────────────────────────────────────────────────────────────────── ⚛ ───
    //   Establishes the default transport layer metadata signatures used by
    //   underlying engines when an outgoing notification payload does not
    //   explicitly declare its origin credentials.
    //
    //   ⚠ Failure to configure valid transport signatures under intensive
    //   asynchronous concurrent operations can lead to immediate upstream
    //   rejections by mail relays or SMS API gateways, terminating the
    //   executing coroutine with an unhandled network exception.
    //
    //   Supported Options:
    //     • "mail" - RFC-compliant sender email address string.
    //     • "sms"  - Alpha-numeric Sender ID string up to 11 characters.
    //

    'from' => [
        'mail' => 'no-reply@example.com',
        'sms'  => 'MOMO',
    ],

    // › Fallback Throttle Policy
    // ─────────────────────────────────────────────────────────────────── ⚛ ───
    //   Defines default rate-limiting parameters applied to notifications
    //   implementing ShouldThrottleInterface. The asynchronous dispatcher reads
    //   these thresholds to enforce sliding-window execution blockades per
    //   recipient identifier.
    //
    //   ⚠ Under a multi-worker Swoole state, utilizing an in-memory storage for
    //   throttling states will cause state drift. Ensure that the framework
    //   scheduler points to a shared Redis atomic lock pool to prevent
    //   concurrency leakage across isolated worker memory boundaries.
    //
    //   Supported Options:
    //     • "max_per_window" - Integer defining maximum allowed delivery attempts.
    //     • "window_seconds" - Lifespan of the sliding tracking matrix in seconds.
    //

    'throttle' => [
        'max_per_window' => 5,
        'window_seconds' => 3600,
    ],
];