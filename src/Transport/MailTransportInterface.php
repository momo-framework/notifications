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
 * Delivers a {@see MailMessage}. Swap the bound implementation for SMTP, an API
 * provider, etc.; the package ships {@see ArrayMailTransport} for local and test use.
 */
interface MailTransportInterface
{
    /**
     * @param non-empty-string $to recipient address
     * @throws \Throwable on a delivery failure (lets the queue retry)
     */
    public function send(MailMessage $message, string $to): void;
}
