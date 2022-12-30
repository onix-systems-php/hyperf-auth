# Hyperf-auth component

Includes the following classes:

- Contract:
  - AssignSocialiteAvatarService;
  - Authenticatable;
  - AuthenticatableProvider;
  - AuthenticatableRepository;
  - CreateSocialiteUserService;
  - TokenGuard;
  - TokenGuardProvider;  
- Controller:
  - AuthController;
  - AuthSocialiteController;
  - WebSocketController;
- DTO:
  - AuthTokensDTO;
  - LoginDTO;
  - SocialiteHandlerDTO;
  - UserSocialiteDTO;
- Guards:
  - JwtGuard.
- Middleware:
  - SessionMiddleware;
- Model:
  - UserSocialite;
- Repository:
  - UserSocialiteRepository;
- Resource:
  - ResourceAuthToken;
  - ResourceLoginLink;
  - ResourceSocial;
- Service:
  - DeleteSocialiteService;
  - GetSocialiteLoginLinkService;
  - LoginUserService;
  - LogoutUserService;
  - PrepareSocialiteProviderService;
  - RefreshTokenService;
  - SocialiteHandlerService;
- AuthManager;
- Session;
- SessionManager.

Install:
```shell script
composer require onix-systems-php/hyperf-auth
```

Publish config and database migrations:
```shell script
php bin/hyperf.php vendor:publish onix-systems-php/hyperf-auth
```

Import auth routes:
```php
require_once './vendor/onix-systems-php/hyperf-auth/publish/routes.php';
```


## Socket.io controller

You can import `ws_routes`, use `WebSocketController` directly and place logic to another controller,
but it is reasonable to extend `WebSocketController` instead.
