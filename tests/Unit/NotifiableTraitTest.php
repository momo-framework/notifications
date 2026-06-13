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

use Momo\Notifications\Tests\Fixtures\TestUser;
use PHPUnit\Framework\TestCase;

final class NotifiableTraitTest extends TestCase
{
    public function testRoutesResolveFromTheMap(): void
    {
        $user = new TestUser('u-1', ['mail' => 'a@example.com', 'sms' => '+100']);

        self::assertSame('a@example.com', $user->routeNotificationFor('mail'));
        self::assertSame('+100', $user->routeNotificationFor('sms'));
    }

    public function testMissingRouteIsNull(): void
    {
        $user = new TestUser('u-1', ['mail' => 'a@example.com']);

        self::assertNull($user->routeNotificationFor('push'));
    }

    public function testNotificationKeyIsExposed(): void
    {
        self::assertSame('u-42', (new TestUser('u-42'))->notificationKey());
    }

    public function testPreferredLocaleDefaultsAndOverrides(): void
    {
        self::assertNull((new TestUser('u-1'))->preferredLocale());
        self::assertSame('ru', (new TestUser('u-1', [], 'ru'))->preferredLocale());
    }
}
