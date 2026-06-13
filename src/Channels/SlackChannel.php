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

namespace Momo\Notifications\Channels;

use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Contracts\NotificationChannelInterface;
use Momo\Notifications\Contracts\NotificationInterface;
use Momo\Notifications\Messages\RoutesToSlack;
use Momo\Notifications\Messages\WebhookMessage;
use Momo\Notifications\Transport\WebhookTransportInterface;

/**
 * Delivers notifications implementing {@see RoutesToSlack} to a Slack incoming
 * webhook. Reuses the webhook transport with a Slack-shaped JSON body.
 */
final class SlackChannel implements NotificationChannelInterface
{
    public function __construct(
        private readonly WebhookTransportInterface $transport,
    ) {
    }

    public function name(): string
    {
        return 'slack';
    }

    public function send(NotifiableInterface $notifiable, NotificationInterface $notification): bool
    {
        if (! $notification instanceof RoutesToSlack) {
            return false;
        }

        $url = $notifiable->routeNotificationFor($this->name());

        if ($url === null) {
            return false;
        }

        $slack   = $notification->toSlack($notifiable);
        $payload = ['text' => $slack->text];

        if ($slack->channel !== null) {
            $payload['channel'] = $slack->channel;
        }

        if ($slack->username !== null) {
            $payload['username'] = $slack->username;
        }

        $this->transport->send(
            new WebhookMessage(
                event: 'slack.message',
                jsonBody: \json_encode($payload, \JSON_THROW_ON_ERROR),
                headers: ['Content-Type' => 'application/json'],
            ),
            $url,
        );

        return true;
    }
}
