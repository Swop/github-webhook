Github WebHook Security Checker
==================

[![Build
Status](https://secure.travis-ci.org/Swop/github-webhook-security-checker.png?branch=master)](http://travis-ci.org/Swop/github-webhook-security-checker)

This library offers a security checker which will verify if the incoming GitHub web hook request is correctly signed.

The provided PSR-7 request will have its `X-Hub-Signature` header checked in order to see if the request was originally performed by GitHub using the correct secret to sign the request.

Installation
------------

The recommended way to install this library is through [Composer](https://getcomposer.org/):

```
composer require "swop/github-webhook-security-checker"
```

Usage
------------

```php
use Swop\GitHub\WebHookSecurityChecker\SecurityChecker;

$checker = new SecurityChecker('MyWebHookSecret');

/** @var \Psr\Http\Message\ServerRequestInterface $request */
if ($checker->check($request)) {
    // Request is correctly signed
}
````

Contributing
------------

See [CONTRIBUTING](https://github.com/Swop/github-webhook-security-checker/blob/master/CONTRIBUTING.md) file.

Original Credits
------------

* [Sylvain MAUDUIT](https://github.com/Swop) ([@Swop](https://twitter.com/Swop)) as main author.


License
------------

This library is released under the MIT license. See the complete license in the bundled [LICENSE](https://github.com/Swop/github-webhook-security-checker/blob/master/LICENSE) file.
