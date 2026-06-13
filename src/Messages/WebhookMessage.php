<?php

/**
 * Part of Momo Framework.
 *
 * © Momo Framework
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Unauthorized copying, modification, or distribution of this file,
 * via any medium, is strictly prohibited without prior written permission
 * from the copyright holder.
 *
 * @author    Vahe Sargsyan <w33bvGL>
 * @copyright Momo Framework
 * @license   AGPL-3.0-or-later <https://www.gnu.org/licenses/agpl-3.0.html>
 * @link      https://github.com/momo-framework
 */

declare(strict_types=1);

namespace Momo\Notifications\Messages;

/**
 * Immutable outbound webhook payload produced by a notification's `toWebhook()`.
 *
 * The body is a pre-serialised JSON string so the value object stays strictly
 * typed (no `mixed` payload); the notification owns the encoding.
 */
final readonly class WebhookMessage
{
    /**
     * @param non-empty-string                $event   logical event name sent as the X-Momo-Event header
     * @param string                          $jsonBody serialised request body
     * @param array<non-empty-string, string> $headers  extra request headers
     */
    public function __construct(
        public string $event,
        public string $jsonBody,
        public array $headers = [],
    ) {
    }
}
