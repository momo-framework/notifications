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

use Momo\Notifications\Messages\MailMessage;
use Momo\Notifications\Transport\MailTransportInterface;
use RuntimeException;

/**
 * Mail transport that always fails — used to assert failure events and retries.
 */
final class ExplodingMailTransport implements MailTransportInterface
{
    public function send(MailMessage $message, string $to): void
    {
        throw new RuntimeException('smtp down');
    }
}
