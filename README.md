[![Build Status](https://api.travis-ci.org/upscalesoftware/http-server-engine.svg?branch=master)](https://travis-ci.org/upscalesoftware/http-server-engine)

HTTP Server Engine for RESTful API
==================================

This package wires up [Zend Diactoros](https://github.com/zendframework/zend-diactoros), [FastRoute](https://github.com/nikic/FastRoute), and [Aura.Di](https://github.com/auraphp/Aura.Di) to power request parsing, routing, and dependency injection of a bare-bones RESTful API.
Each library is the most lightweight/popular implementation in its class.


## Usage

The intended use is RESTful web services based on the [HTTP server skeleton](https://github.com/upscalesoftware/http-server-skeleton).
It defines the project structure, router configuration, and the entry point that bootstraps the engine.


## Performance

Speed and minimalism are the objectives of the project. It comes with very little source code.

Total overheard of the engine is ~4 ms (0.004 sec) measured on a laptop.
The estimates are based on execution of an empty handler dispatched among 100 declared routes.


## License

Licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).
