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

use Composer\ClassMapGenerator\ClassMapGenerator;
use Composer\InstalledVersions;
use Marmot\Brick\CacheManager;
use Marmot\Brick\Exceptions\PackageContainsNoBrickException;
use Marmot\Brick\Exceptions\PackageContainsSeveralBrickException;
use ReflectionClass;
use ReflectionException;

/**
 * This class is used to load all Bricks with their Events and Services
 */
final class BrickLoader
{
    private const PACKAGE_TYPE = 'marmot-brick';
    private const CACHE_DIR    = 'bricks';

    /**
     * @var BrickPresenter[]
     */
    private array $bricks = [];

    /**
     * Load all installed Bricks
     *
     * @throws PackageContainsSeveralBrickException
     * @throws PackageContainsNoBrickException
     */
    public function loadBricks(): void
    {
        $packages = InstalledVersions::getInstalledPackagesByType(self::PACKAGE_TYPE);

        foreach ($packages as $package) {
            try {
                $brick = $this->loadBrick($package);
                if ($brick) {
                    $this->bricks[] = $brick;
                }
            } catch (ReflectionException) {
                // Ignore ReflectionException, but let pass others
            }
        }

        CacheManager::instance()->save(self::CACHE_DIR, BrickLoader::class, $this->bricks);

        $this->initializeBricks();
    }

    public function loadFromCache(): void
    {
        /** @var BrickPresenter[] */
        $this->bricks = CacheManager::instance()->load(self::CACHE_DIR, BrickLoader::class);

        $this->initializeBricks();
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    private function initializeBricks(): void
    {
        // TODO : load Events, Services and EventListeners

        // TODO : call initialize on each Bricks
    }

    /**
     * @param string $package
     * @return BrickPresenter|null
     * @throws PackageContainsNoBrickException
     * @throws PackageContainsSeveralBrickException
     * @throws ReflectionException
     */
    private function loadBrick(string $package): ?BrickPresenter
    {
        $install_path = InstalledVersions::getInstallPath($package);
        if ($install_path === null) {
            return null; // Package is not installed, ignore it
        }

        $map = ClassMapGenerator::createMap($install_path);

        /** @var ReflectionClass<Brick>|null $brick */
        $brick = null;
        /** @var ReflectionClass[] $class_map */
        $class_map = [];

        foreach ($map as $symbol => $_path) { // For each class in Brick package
            $ref = new ReflectionClass($symbol);

            if ($ref->isSubclassOf(Brick::class)) { // If class is Brick
                if ($brick === null) {
                    $brick = $ref;
                    continue;
                } else {
                    throw new PackageContainsSeveralBrickException($package, $brick->getName(), $ref->getName());
                }
            }

            $class_map[] = $ref;
        }

        if ($brick === null) {
            throw new PackageContainsNoBrickException($package);
        }

        return new BrickPresenter(
            $package,
            $brick,
            $class_map,
        );
    }
}
