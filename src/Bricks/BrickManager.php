<?php
/**
 * MIT License
 *
 * Copyright (c) 2023-Present Kevin Traini
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Marmot\Brick\Bricks;

use Marmot\Brick\Services\Service;
use ReflectionClass;

#[Service(autoload: false)]
final class BrickManager
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * !DANGER! Be careful and very sure before using this method
     */
    public static function flush(): void
    {
        self::$instance = null;
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    /**
     * @var BrickPresenter[]
     */
    private array $bricks = [];

    public function addBrick(BrickPresenter $brick): void
    {
        $this->bricks[] = $brick;
    }

    public function addBricks(BrickPresenter ...$bricks): void
    {
        foreach ($bricks as $brick) {
            $this->addBrick($brick);
        }
    }

    public function getBrick(string $package): ?BrickPresenter
    {
        foreach ($this->bricks as $brick) {
            if ($brick->package === $package) {
                return $brick;
            }
        }

        return null;
    }

    /**
     * @return BrickPresenter[]
     */
    public function getBricks(): array
    {
        return $this->bricks;
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    /**
     * @psalm-param ?callable(ReflectionClass): bool $filter
     * @return ReflectionClass[]
     */
    public function getClassMap(?callable $filter): array
    {
        $class_map = [];
        foreach ($this->bricks as $brick) {
            $class_map = array_merge($class_map, $brick->getClassMap($filter));
        }

        return $class_map;
    }
}
