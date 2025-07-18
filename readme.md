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
composer require vkoori/jwt-auth

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Vkoori\JwtAuth\Providers\AuthServiceProvider" --tag=config
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

