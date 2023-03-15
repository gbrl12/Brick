# Marmot/Brick

[![Test](https://github.com/Marmot-framework/Brick/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/Marmot-framework/Brick/actions/workflows/test.yml)

> A house is built with bricks

This composer plugin aims to provide bricks to Marmot core in goal to build a nice framework. Each bricks will provide a
new behavior to Marmot through events, services and commands.

## Create a new brick

You want to add a new behavior to Marmot ? Let's build it !

The very first thing to do is to require this package :

```shell
composer require marmot/brick
```

You can begin to work !

A brick consists on a collection of `Service`, `Event`, `Command` and others. It's recommended to follow this directory
structure :

```
your-brick/
├── config/
│   ├── ...
│   ├── services.yml
│   └── brick.yml
├── src/
│   ├── Commands
│   ├── Events
│   └── Services
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

The two last directories are needed only if your brick will render some things. For that you will need `Marmot/MdGen`
and `Marmot/Router` bricks.

Now you can add all your dependencies in your `composer.json`. Naturally, a brick can be composed of bricks, so you can
add some bricks in your dependencies.

The next step is to configure your brick ! For that, edit `config/brick.yml`. This file contains all needed information
required by the core to understand and use your brick. You can see this file like cement.

```yaml
brick:
  class: Name\\Space\\Of\\Your\\Brick
```

... more later
