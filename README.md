# Marmot/Brick

[![Test](https://github.com/Marmot-framework/Brick/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/Marmot-framework/Brick/actions/workflows/test.yml)

> A house is built with bricks

This composer plugin aims to provide bricks to Marmot core in goal to build a nice framework. Each bricks will provide a
new behavior to Marmot through events, services and commands.

## Create a new brick

You want to add a new behavior to Marmot ? Let's build it !

In the following, we will create a Brick name HelloBrick with an Event, a Service and a Command.

### Initialize the project

The very first thing to do is to create a composer project :

```shell
# Create a directory and go into
mkdir HelloBrick && cd HelloBrick
# Init composer
composer init
```

For your composer configuration, the package name will be `<vendor>/hello-brick`, and you can set autoload this way :

```json
{
    "autoload": {
        "psr-4": {
            "<Vendor>\\HelloBrick": "src/"
        }
    }
}
```

Then, require the Brick library :

```shell
composer require marmot/brick
```

A brick consists on a collection of `Service`, `Event`, `Command` and others files. It's recommended to follow this
directory structure :

```
your-brick/
├── config/
│   ├── ...
│   ├── services.yml
│   └── brick.yml
├── src/
│   ├── Commands/
│   ├── Events/
│   ├── Services/
│   └── Brick.php
├── tests/
├── public/
├── view/
```

A little explanation on each directory :

- `config` contains all YAML config files and the most important one : `brick.yml` (more on that later)
- `src` contains all your PHP source files
- `tests` really need to explain ?
- `public` contains public assets, like css, javascript, images, ...
- `view` contains templates for rendering

Now open `config/brick.yml` and write that :

```yaml
brick:
  name: HelloBrick
  description: A Brick that say Hello to World!
  class: <Vendor>\\HelloBrick\\HelloBrick
```

Name and description are optional, but are useful for documentation. The only required field is class, its value must be
the name of the class (with its namespace) implementing the interface `BrickInterface`.

### Implement `BrickInterface`

The key component of a Brick is the `BrickInterface`. To create a new Brick, you must declare a class implementing this
interface. Let's do that :

```php
<?php

namespace <Vendor>\HelloBrick;

use Marmot\Brick\BrickInterface;

class HelloBrick implements BrickInterface
{
}
```

And *voilà!* We now have our great new Brick! Well, currently this Brick doesn't do anything. But we will add all the
needed step by step. And the first thing will be : an Event.

### Adding an Event

In Marmot Bricks, Events can be dispatch by the EventManager and listened by Services (we will see Service later). In
our project we want a custom Event that tell we want to say 'Hello!' :

```php
<?php

namespace <Vendor>\HelloBrick\Events;

use Marmot\Brick\Events\Event;

#[Event]
class SayHelloEvent
{
}
```

That's all! Actually not. Did you remember the directory structure earlier? Well, when we create a new Event, the
corresponding class must be in the `src/Events` directory. That's way, our Brick will know the existence of our Event (
it will be the same for Services and Commands).
