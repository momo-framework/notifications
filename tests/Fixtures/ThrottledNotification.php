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

namespace Momo\Notifications\Tests\Fixtures;

use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Contracts\NotificationInterface;
use Momo\Notifications\Contracts\ShouldThrottleInterface;
use Momo\Notifications\Messages\MailMessage;
use Momo\Notifications\Messages\RoutesToMail;

/**
 * A mail notification capped at two deliveries per hour per recipient.
 */
final class ThrottledNotification implements
    NotificationInterface,
    RoutesToMail,
    ShouldThrottleInterface
{
    public function via(NotifiableInterface $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(NotifiableInterface $notifiable): MailMessage
    {
        return new MailMessage(subject: 'Digest', body: 'Your activity digest.');
    }

    public function throttleKey(NotifiableInterface $notifiable): string
    {
        return 'digest:' . $notifiable->notificationKey();
    }

    public function throttleMaxPerWindow(): int
    {
        return 2;
    }

    public function throttleWindowSeconds(): int
    {
        return 3600;
    }
}
