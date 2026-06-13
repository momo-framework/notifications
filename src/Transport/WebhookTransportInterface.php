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

namespace Momo\Notifications\Transport;

use Momo\Notifications\Messages\WebhookMessage;

/**
 * POSTs a {@see WebhookMessage} to a URL. Shared by the `webhook` and `slack`
 * channels. Bind a coroutine HTTP client in place of {@see ArrayWebhookTransport}.
 */
interface WebhookTransportInterface
{
    /**
     * @param non-empty-string $url
     * @throws \Throwable on a non-2xx response or transport error
     */
    public function send(WebhookMessage $message, string $url): void;
}
