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

use Momo\Notifications\Contracts\NotifiableInterface;
use Momo\Notifications\Support\Notifiable;

/**
 * A recipient fixture wiring channel routes through the {@see Notifiable} trait.
 */
final class TestUser implements NotifiableInterface
{
    use Notifiable;

    /**
     * @param non-empty-string                                $id
     * @param array<non-empty-string, non-empty-string|null>  $routes
     * @param non-empty-string|null                           $locale
     */
    public function __construct(
        private readonly string $id,
        private readonly array $routes = [],
        private readonly ?string $locale = null,
    ) {
    }

    public function notificationKey(): string
    {
        return $this->id;
    }

    public function preferredLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @return array<non-empty-string, non-empty-string|null>
     */
    protected function notificationRoutes(): array
    {
        return $this->routes;
    }
}
