# JwtAuth for Laravel

A fully stateless JWT authentication guard and provider for Laravel.  
Supports access/refresh tokens, automatic cookie injection, multi-source token parsing (header, query, cookie), and token revocation.

---

## ✨ Features

- 🔐 **Custom Laravel guard + provider** (fully stateless)
- ♻️ **Refresh token support**
- 🍪 **Cookie-based tokens (HttpOnly, optional)**
- 📩 **Header / Query string token support**
- 🔄 **Token revocation**
- 💡 **Simple trait-based token issuing**
- ⚡️ **Octane-ready**

---

## 📦 Installation

```bash
composer require vkoori/laravel-stateless-auth
```

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Vkoori\JwtAuth\AuthServiceProvider"
```

This will publish `config/jwt-guard.php`.

also [read optionally](https://github.com/vkoori/laravel-jwt)

### 🛡 Register the Guard & Provider

In your `config/auth.php`:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt-auth',
        'provider' => 'jwt-users',
    ],
],

'providers' => [
    'jwt-users' => [
        'driver' => 'jwt-auth-provider',
        'model' => App\Models\User::class,
    ],
],
```

### 👤 Token Support on User Model

Your `User` model must use the provided trait:

```php
use Vkoori\JwtAuth\Auth\Traits\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}
```

### 🧩 Custom Cache Driver for JWT Token Storage

To prevent JWT tokens from being removed during global cache clears (`php artisan cache:clear`), you can isolate token storage using a **custom cache store**.

#### 🔧 Configuration

*Use one of the existing cache stores, or define a dedicated store for JWT.*

1. Add a new cache store in `config/cache.php`:

```php
'stores' => [
    // Other cache stores...

    'redis_jwt' => [
        'driver' => 'redis',
        'connection' => env('REDIS_JWT_CONNECTION', 'jwt'),
        'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
    ],
],
```

2. Define a dedicated Redis connection in `config/database.php`:

```php
'redis' => [

    // Other connections...

    'jwt' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_JWT_DB', '2'),
    ],
],
```

3. Set the custom driver on your `Authenticatable` model using the `HasApiTokens` trait:

```php
use Vkoori\JwtAuth\Auth\Traits\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected ?string $jwtCacheDriver = 'redis_jwt';
}
```

#### 💡 Why This Matters

By using a dedicated Redis cache store for JWT tokens:

- php artisan cache:clear won’t wipe out active tokens
- You can still manually clear tokens when needed:

```bash
php artisan cache:clear redis_jwt
```

This is especially useful when `enable_revoke` is set to `true` in your config, ensuring users are logged out securely while preserving system-wide cache stability.
