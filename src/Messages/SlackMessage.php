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
 * Immutable Slack payload produced by a notification's `toSlack()`. Delivered as
 * a JSON body to a Slack incoming-webhook URL.
 */
final readonly class SlackMessage
{
    /**
     * @param non-empty-string      $text
     * @param non-empty-string|null $channel override the webhook's default channel, e.g. "#orders"
     * @param non-empty-string|null $username override the webhook's display name
     */
    public function __construct(
        public string $text,
        public ?string $channel = null,
        public ?string $username = null,
    ) {
    }
}
