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

use Momo\Notifications\Support\MutableClock;
use Momo\Notifications\Throttle\InMemoryThrottler;
use PHPUnit\Framework\TestCase;

final class InMemoryThrottlerTest extends TestCase
{
    public function testAllowsUpToTheLimitThenDenies(): void
    {
        $throttler = new InMemoryThrottler(new MutableClock(0));

        self::assertTrue($throttler->allow('k', 2, 60));
        self::assertTrue($throttler->allow('k', 2, 60));
        self::assertFalse($throttler->allow('k', 2, 60));
    }

    public function testDifferentKeysAreIndependent(): void
    {
        $throttler = new InMemoryThrottler(new MutableClock(0));

        self::assertTrue($throttler->allow('a', 1, 60));
        self::assertFalse($throttler->allow('a', 1, 60));
        self::assertTrue($throttler->allow('b', 1, 60));
    }

    public function testQuotaResetsAfterWindowElapses(): void
    {
        $clock     = new MutableClock(0);
        $throttler = new InMemoryThrottler($clock);

        self::assertTrue($throttler->allow('k', 1, 60));
        self::assertFalse($throttler->allow('k', 1, 60));

        $clock->advance(61);

        self::assertTrue($throttler->allow('k', 1, 60));
    }

    public function testDeniedAttemptDoesNotConsumeFutureQuota(): void
    {
        $clock     = new MutableClock(100);
        $throttler = new InMemoryThrottler($clock);

        self::assertTrue($throttler->allow('k', 1, 60));
        self::assertFalse($throttler->allow('k', 1, 60));
        self::assertFalse($throttler->allow('k', 1, 60));

        $clock->advance(61);

        self::assertTrue($throttler->allow('k', 1, 60));
    }
}
