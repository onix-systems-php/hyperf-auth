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
