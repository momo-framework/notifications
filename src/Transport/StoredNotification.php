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

use Momo\Notifications\Messages\BroadcastMessage;

/**
 * An in-app notification persisted in a recipient's inbox.
 */
final readonly class StoredNotification
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $notifiableKey
     * @param int<0, max>      $createdAt
     */
    public function __construct(
        public string $id,
        public string $notifiableKey,
        public BroadcastMessage $message,
        public int $createdAt,
        public bool $read = false,
    ) {
    }

    /**
     * A copy marked as read.
     */
    public function asRead(): self
    {
        return new self($this->id, $this->notifiableKey, $this->message, $this->createdAt, true);
    }
}
