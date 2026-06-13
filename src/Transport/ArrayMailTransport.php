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

use Momo\Notifications\Messages\MailMessage;

/**
 * In-memory mail transport: records every message instead of sending it. The
 * default binding for local development and tests.
 */
final class ArrayMailTransport implements MailTransportInterface
{
    /** @var list<array{to: non-empty-string, message: MailMessage}> */
    private array $sent = [];

    public function send(MailMessage $message, string $to): void
    {
        $this->sent[] = ['to' => $to, 'message' => $message];
    }

    /**
     * Every message recorded so far, oldest first.
     *
     * @return list<array{to: non-empty-string, message: MailMessage}>
     */
    public function sent(): array
    {
        return $this->sent;
    }
}
