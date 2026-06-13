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
    /*
     | Channels available to notifications. A notification's via() may only
     | return names present here (each must be registered in the
     | ChannelRegistry by the service provider or an application provider).
     */
    'channels' => ['mail', 'sms', 'push', 'broadcast', 'slack', 'webhook'],

    /*
     | Default sender identities used by transports when a message does not set
     | its own.
     */
    'from' => [
        'mail' => 'no-reply@example.com',
        'sms'  => 'MOMO',
    ],

    /*
     | Fallback throttle window for notifications that implement
     | ShouldThrottleInterface without overriding these (informational defaults
     | for application code; the dispatcher reads the values off the notification).
     */
    'throttle' => [
        'max_per_window' => 5,
        'window_seconds' => 3600,
    ],
];
