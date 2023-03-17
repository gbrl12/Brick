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

namespace Marmot\Brick;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Composer\InstalledVersions;
use Marmot\Brick\Events\Event;
use Marmot\Brick\Exceptions\PackageContainsNoBrickException;
use Marmot\Brick\Exceptions\PackageContainsSeveralBrickException;
use Marmot\Brick\Services\Service;
use ReflectionClass;
use ReflectionException;

/**
 * This class is used to load all Bricks with their Events and Services
 */
final class BrickLoader
{
    private const PACKAGE_TYPE = 'marmot-brick';

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
                $this->loadBrick($package);
            } catch (ReflectionException) {
                // Ignore ReflectionException, but let pass others
            }
        }
    }

    /**
     * @param string $package
     * @return void
     * @throws PackageContainsSeveralBrickException
     * @throws ReflectionException
     * @throws PackageContainsNoBrickException
     */
    private function loadBrick(string $package): void
    {
        $install_path = InstalledVersions::getInstallPath($package);
        if ($install_path === null) {
            return; // Package is not installed, ignore it
        }

        $map = ClassMapGenerator::createMap($install_path);

        /** @var ReflectionClass<Brick>|null $brick */
        $brick     = null;
        $_services = []; // TODO : remove the _, it's here just for silent psalm
        $_events   = [];

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

            $attrs = $ref->getAttributes(Service::class);
            if (!empty($attrs)) { // If class is Service
                $_services[] = $ref;
                continue;
            }

            $attrs = $ref->getAttributes(Event::class);
            if (!empty($attrs)) { // If class is Event
                $_events[] = $ref;
            }
        }

        if ($brick === null) {
            throw new PackageContainsNoBrickException($package);
        }

        // TODO : store a Brick reference
    }
}
