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

final class CacheManager
{
    public function __construct(
        private readonly string $cache_dir,
        private readonly Mode   $mode,
    ) {
    }

    public function save(string $path, string $name, object $object): void
    {
        if ($this->mode == Mode::PROD) { // Can save only in production
            $content = serialize($object);

            file_put_contents($this->cache_dir . '/' . $this->getFileName($path, $name), $content);
        }
    }

    /**
     * @param string $path
     * @param string $name
     * @return mixed|null
     */
    public function load(string $path, string $name): mixed
    {
        if ($this->mode == Mode::PROD) {
            $content = file_get_contents($this->cache_dir . '/' . $this->getFileName($path, $name));

            if ($content) {
                return unserialize($content);
            }
        }

        return null;
    }

    // _.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-._.-.

    private function getFileName(string $path, string $name): string
    {
        $npath = hash('sha512', $path);
        $nname = hash('sha512', $name);

        return $npath . '/' . $nname;
    }
}
