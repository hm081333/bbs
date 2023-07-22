<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this in your .env file, as it will be used to sign
    | your tokens. A helper command is provided for this:
    | `php artisan jwt:secret`
    |
    | Note: This will be used for Symmetric algorithms only (HMAC),
    | since RSA and ECDSA use a private/public key combo (See below).
    |
    */

    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Keys
    |--------------------------------------------------------------------------
    |
    | The algorithm you are using, will determine whether your tokens are
    | signed with a random string (defined in `JWT_SECRET`) or using the
    | following public & private keys.
    |
    | Symmetric Algorithms:
    | HS256, HS384 & HS512 will use `JWT_SECRET`.
    |
    | Asymmetric Algorithms:
    | RS256, RS384 & RS512 / ES256, ES384 & ES512 will use the keys below.
    |
    */

    'keys' => [

        /*
        |--------------------------------------------------------------------------
        | Public Key
        |--------------------------------------------------------------------------
        |
        | A path or resource to your public key.
        |
        | E.g. 'file://path/to/public/key'
        |
        */

        'public' => env('JWT_PUBLIC_KEY'),

        /*
        |--------------------------------------------------------------------------
        | Private Key
        |--------------------------------------------------------------------------
        |
        | A path or resource to your private key.
        |
        | E.g. 'file://path/to/private/key'
        |
        */

        'private' => env('JWT_PRIVATE_KEY'),

        /*
        |--------------------------------------------------------------------------
        | Passphrase
        |--------------------------------------------------------------------------
        |
        | The passphrase for your private key. Can be null if none set.
        |
        */

        'passphrase' => env('JWT_PASSPHRASE'),

    ],

    /*
    |--------------------------------------------------------------------------
    | JWT 存活时间
    |--------------------------------------------------------------------------
    |
    | 指定令牌的有效时间长度（以分钟为单位）。
    | 默认为 1 小时。
    |
    | 您还可以将其设置为 null，以生成永不过期的令牌。
    | 有些人可能想要这种行为，例如 一个移动应用程序。
    | 不特别推荐这样做，因此请确保您有适当的系统来在必要时撤销令牌。
    | 注意：如果将其设置为 null，则应从“required_claims”列表中删除“exp”元素。
    |
    */

    //'ttl' => env('JWT_TTL', 60),
    //'ttl' => env('JWT_TTL', 30 * 24 * 60),
    //'ttl' => env('JWT_TTL', 365 * 24 * 60),
    'ttl' => null,

    /*
    |--------------------------------------------------------------------------
    | Refresh 存活时间
    |--------------------------------------------------------------------------
    |
    | 指定可以刷新令牌的时间长度（以分钟为单位）。
    | I.E.
    | 用户可以在创建原始令牌后 2 周内刷新其令牌，直到必须重新进行身份验证。
    | 默认为 2 周。
    |
    | 您还可以将其设置为 null，以产生无限的刷新时间。
    | 有些人可能想要这个而不是永不过期的令牌，例如 一个移动应用程序。
    | 不特别推荐这样做，因此请确保您有适当的系统来在必要时撤销令牌。
    |
    */

    //'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),
    //'refresh_ttl' => env('JWT_REFRESH_TTL', 2 * 7 * 24 * 60),
    'refresh_ttl' => null,

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm that will be used to sign the token.
    |
    */

    'algo' => env('JWT_ALGO', Tymon\JWTAuth\Providers\JWT\Provider::ALGO_HS256),

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    |
    | Specify the required claims that must exist in any token.
    | A TokenInvalidException will be thrown if any of these claims are not
    | present in the payload.
    |
    */

    'required_claims' => [
        'iss',
        'iat',
        //'exp',
        'nbf',
        'sub',
        'jti',
        'account_type',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistent Claims
    |--------------------------------------------------------------------------
    |
    | 指定刷新令牌时要保留的声明密钥。
    | 除了这些声明之外，还将自动保留 `sub` 和 `iat`。
    |
    | 注意：如果声明不存在，则该声明将被忽略。
    |
    */

    'persistent_claims' => [
         'account_type',
    ],

    /*
    |--------------------------------------------------------------------------
    | Lock Subject
    |--------------------------------------------------------------------------
    |
    | This will determine whether a `prv` claim is automatically added to
    | the token. The purpose of this is to ensure that if you have multiple
    | authentication models e.g. `App\User` & `App\OtherPerson`, then we
    | should prevent one authentication request from impersonating another,
    | if 2 tokens happen to have the same id across the 2 different models.
    |
    | Under specific circumstances, you may want to disable this behaviour
    | e.g. if you only have one authentication model, then you would save
    | a little on token size.
    |
    */

    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | Leeway
    |--------------------------------------------------------------------------
    |
    | This property gives the jwt timestamp claims some "leeway".
    | Meaning that if you have any unavoidable slight clock skew on
    | any of your servers then this will afford you some level of cushioning.
    |
    | This applies to the claims `iat`, `nbf` and `exp`.
    |
    | Specify in seconds - only if you know you need it.
    |
    */

    'leeway' => env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | 启用黑名单
    |--------------------------------------------------------------------------
    |
    | In order to invalidate tokens, you must have the blacklist enabled.
    | If you do not want or need this functionality, then set this to false.
    |
    */

    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    | -------------------------------------------------------------------------
    | 黑名单的宽限期
    | -------------------------------------------------------------------------
    |
    | When multiple concurrent requests are made with the same JWT,
    | it is possible that some of them fail, due to token regeneration
    | on every request.
    |
    | 以秒为单位设置宽限时间，避免并发请求失败。
    |
    */

    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | Cookies加密
    |--------------------------------------------------------------------------
    |
    | By default Laravel encrypt cookies for security reason.
    | If you decide to not decrypt cookies, you will have to configure Laravel
    | to not encrypt your cookie token by adding its name into the $except
    | array available in the middleware "EncryptCookies" provided by Laravel.
    | see https://laravel.com/docs/master/responses#cookies-and-encryption
    | for details.
    |
    | 如果您想要解密cookie，请将其设置为true。
    |
    */

    'decrypt_cookies' => false,

    /*
    |--------------------------------------------------------------------------
    | 供应商
    |--------------------------------------------------------------------------
    |
    | 指定整个包中使用的各种提供程序。
    |
    */

    'providers' => [

        /*
        |--------------------------------------------------------------------------
        | JWT提供者
        |--------------------------------------------------------------------------
        |
        | 指定用于创建和解码令牌的提供程序。
        |
        */

        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,

        /*
        |--------------------------------------------------------------------------
        | 身份验证提供者
        |--------------------------------------------------------------------------
        |
        | 指定用于对用户进行身份验证的提供者。
        |
        */

        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,

        /*
        |--------------------------------------------------------------------------
        | 存储提供商
        |--------------------------------------------------------------------------
        |
        | 指定用于在黑名单中存储令牌的提供商。
        |
        */

        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,

    ],

];
