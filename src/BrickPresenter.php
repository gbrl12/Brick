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

use ReflectionClass;

final class BrickPresenter
{
    /**
     * @param ReflectionClass          $brick
     * @param ReflectionClass[]        $services
     * @param ReflectionClass[]        $events
     * @param EventListenerPresenter[] $listeners
     */
    public function __construct(
        private readonly ReflectionClass $brick,
        private readonly array           $services,
        private readonly array           $events,
        private readonly array           $listeners,
    ) {
    }

    /**
     * @return ReflectionClass
     */
    public function getBrick(): ReflectionClass
    {
        return $this->brick;
    }

    /**
     * @return ReflectionClass[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return ReflectionClass[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @return EventListenerPresenter[]
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function __serialize(): array
    {
        return [
            'brick'     => serialize($this->brick),
            'services'  => serialize($this->services),
            'events'    => serialize($this->events),
            'listeners' => serialize($this->listeners),
        ];
    }

    /**
     * @param array<string, string> $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        /** @var ReflectionClass */
        $this->brick = unserialize($data['brick']);
        /** @var ReflectionClass[] */
        $this->services = unserialize($data['services']);
        /** @var ReflectionClass[] */
        $this->events = unserialize($data['events']);
        /** @var EventListenerPresenter[] */
        $this->listeners = unserialize($data['listeners']);
    }
}
