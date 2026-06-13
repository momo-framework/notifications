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

use Momo\Notifications\Messages\PushMessage;

/**
 * In-memory push transport: records every message. Default for local/test use.
 */
final class ArrayPushTransport implements PushTransportInterface
{
    /** @var list<array{token: non-empty-string, message: PushMessage}> */
    private array $sent = [];

    public function send(PushMessage $message, string $deviceToken): void
    {
        $this->sent[] = ['token' => $deviceToken, 'message' => $message];
    }

    /**
     * @return list<array{token: non-empty-string, message: PushMessage}>
     */
    public function sent(): array
    {
        return $this->sent;
    }
}
