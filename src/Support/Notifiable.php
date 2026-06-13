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

namespace Momo\Notifications\Support;

/**
 * Default {@see \Momo\Notifications\Contracts\NotifiableInterface} implementation
 * for models. Use it on any entity that should receive notifications:
 *
 *   final class User implements NotifiableInterface
 *   {
 *       use Notifiable;
 *
 *       public function notificationKey(): string { return $this->id; }
 *
 *       protected function notificationRoutes(): array
 *       {
 *           return ['mail' => $this->email, 'sms' => $this->phone];
 *       }
 *   }
 */
trait Notifiable
{
    /**
     * Stable identity of this recipient. Must be implemented by the model.
     *
     * @return non-empty-string
     */
    abstract public function notificationKey(): string;

    public function routeNotificationFor(string $channel): ?string
    {
        return $this->notificationRoutes()[$channel] ?? null;
    }

    public function preferredLocale(): ?string
    {
        return null;
    }

    /**
     * Map of channel name → destination address. Override per model.
     *
     * @return array<non-empty-string, non-empty-string|null>
     */
    protected function notificationRoutes(): array
    {
        return [];
    }
}
