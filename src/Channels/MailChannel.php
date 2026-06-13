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
use Momo\Notifications\Messages\RoutesToMail;
use Momo\Notifications\Transport\MailTransportInterface;

/**
 * Delivers notifications implementing {@see RoutesToMail} over e-mail.
 */
final class MailChannel implements NotificationChannelInterface
{
    public function __construct(
        private readonly MailTransportInterface $transport,
    ) {
    }

    public function name(): string
    {
        return 'mail';
    }

    public function send(NotifiableInterface $notifiable, NotificationInterface $notification): bool
    {
        if (! $notification instanceof RoutesToMail) {
            return false;
        }

        $to = $notifiable->routeNotificationFor($this->name());

        if ($to === null) {
            return false;
        }

        $this->transport->send($notification->toMail($notifiable), $to);

        return true;
    }
}
