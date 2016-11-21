Github WebHook
==================

[![Build
Status](https://secure.travis-ci.org/Swop/github-webhook.png?branch=master)](http://travis-ci.org/Swop/github-webhook)

This library offers a set of tools which could become handy when dealing with GitHub web hook requests.

Installation
------------

The recommended way to install this library is through [Composer](https://getcomposer.org/):

```
composer require "swop/github-webhook"
```

Usage
------------

### Payload signature checking

The `SignatureValidator` will verify if the incoming GitHub web hook request is correctly signed.

```php
use Swop\GitHubWebHook\Security\SignatureValidator;

$validator = new SignatureValidator();

/** @var \Psr\Http\Message\ServerRequestInterface $request */
if ($validator->validate($request, 'secret')) {
    // Request is correctly signed
}
````

### GitHub event object creation
The `GitHubEventFactory` can build GitHubEvent objects representing the GitHub event.

```php
use Swop\GitHubWebHook\Event\GitHubEventFactory;

$factory = new GitHubEventFactory();

/** @var \Psr\Http\Message\ServerRequestInterface $request */
$gitHubEvent = $factory->buildFromRequest(RequestInterface $request);

$gitHubEvent->getType(); // Event type
$gitHubEvent->getPayload(); // Event deserialized payload
````

Contributing
------------

See [CONTRIBUTING](https://github.com/Swop/github-webhook/blob/master/CONTRIBUTING.md) file.

Original Credits
------------

* [Sylvain MAUDUIT](https://github.com/Swop) ([@Swop](https://twitter.com/Swop)) as main author.


License
------------

This library is released under the MIT license. See the complete license in the bundled [LICENSE](https://github.com/Swop/github-webhook/blob/master/LICENSE) file.
