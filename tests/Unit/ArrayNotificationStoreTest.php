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

use Momo\Notifications\Messages\BroadcastMessage;
use Momo\Notifications\Support\MutableClock;
use Momo\Notifications\Transport\ArrayNotificationStore;
use PHPUnit\Framework\TestCase;

final class ArrayNotificationStoreTest extends TestCase
{
    public function testStoreAndListMostRecentFirst(): void
    {
        $store = new ArrayNotificationStore(new MutableClock(1_000));

        $store->store('u-1', new BroadcastMessage('first'));
        $store->store('u-1', new BroadcastMessage('second'));

        $items = $store->forNotifiable('u-1');

        self::assertCount(2, $items);
        self::assertSame('second', $items[0]->message->type);
        self::assertSame('first', $items[1]->message->type);
        self::assertSame(1_000, $items[0]->createdAt);
    }

    public function testInboxIsScopedToRecipient(): void
    {
        $store = new ArrayNotificationStore(new MutableClock(0));
        $store->store('u-1', new BroadcastMessage('a'));
        $store->store('u-2', new BroadcastMessage('b'));

        self::assertCount(1, $store->forNotifiable('u-1'));
        self::assertCount(1, $store->forNotifiable('u-2'));
    }

    public function testUnreadCountAndMarkAsRead(): void
    {
        $store = new ArrayNotificationStore(new MutableClock(0));
        $id    = $store->store('u-1', new BroadcastMessage('a'));
        $store->store('u-1', new BroadcastMessage('b'));

        self::assertSame(2, $store->unreadCount('u-1'));
        self::assertTrue($store->markAsRead($id));
        self::assertSame(1, $store->unreadCount('u-1'));
    }

    public function testMarkUnknownIdReturnsFalse(): void
    {
        $store = new ArrayNotificationStore(new MutableClock(0));

        self::assertFalse($store->markAsRead('nope'));
    }
}
