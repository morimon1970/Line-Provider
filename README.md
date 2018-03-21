# Instagram Provider

## Installation

```
composer require socialite-manager/line-provider
```

## Usage

```php
use Socialite\Provider\LineProvider;
use Socialite\Socialite;

Socialite::driver(LineProvider::class, $config);
```
