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

namespace Momo\Notifications\Tests\Unit;

use Momo\Notifications\Channels\MailChannel;
use Momo\Notifications\Dispatcher\ChannelRegistry;
use Momo\Notifications\Exceptions\ChannelNotFoundException;
use Momo\Notifications\Transport\ArrayMailTransport;
use PHPUnit\Framework\TestCase;

final class ChannelRegistryTest extends TestCase
{
    public function testRegistersAndResolvesByName(): void
    {
        $channel  = new MailChannel(new ArrayMailTransport());
        $registry = new ChannelRegistry([$channel]);

        self::assertTrue($registry->has('mail'));
        self::assertSame($channel, $registry->get('mail'));
    }

    public function testUnknownChannelReportsAbsent(): void
    {
        $registry = new ChannelRegistry();

        self::assertFalse($registry->has('sms'));
    }

    public function testGettingUnknownChannelThrows(): void
    {
        $registry = new ChannelRegistry();

        $this->expectException(ChannelNotFoundException::class);

        $registry->get('sms');
    }

    public function testLaterRegistrationOverridesEarlier(): void
    {
        $first  = new MailChannel(new ArrayMailTransport());
        $second = new MailChannel(new ArrayMailTransport());

        $registry = new ChannelRegistry([$first]);
        $registry->register($second);

        self::assertSame($second, $registry->get('mail'));
    }
}
