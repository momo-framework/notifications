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

namespace Momo\Notifications\Throttle;

use Momo\Notifications\Contracts\ClockInterface;
use Momo\Notifications\Contracts\ThrottlerInterface;

/**
 * Process-local sliding-window throttler. Keeps the hit timestamps per key and
 * prunes those older than the window on each call.
 *
 * COROUTINE SAFETY: `allow()` has no I/O suspension point, so under Swoole's
 * cooperative scheduler it is atomic with respect to sibling coroutines in the
 * same worker. Cross-worker throttling needs a shared store (e.g. Redis).
 */
final class InMemoryThrottler implements ThrottlerInterface
{
    /** @var array<non-empty-string, list<int<0, max>>> hit timestamps per key */
    private array $hits = [];

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function allow(string $key, int $maxPerWindow, int $windowSeconds): bool
    {
        $now    = $this->clock->now();
        $cutoff = $now - $windowSeconds;

        $recent = \array_values(\array_filter(
            $this->hits[$key] ?? [],
            static fn (int $timestamp): bool => $timestamp > $cutoff,
        ));

        if (\count($recent) >= $maxPerWindow) {
            $this->hits[$key] = $recent;

            return false;
        }

        $recent[]         = $now;
        $this->hits[$key] = $recent;

        return true;
    }
}
