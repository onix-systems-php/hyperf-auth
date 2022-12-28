# Hyperf-auth component

Includes the following classes:
 
- DTO:
  - AuthTokensDTO.
- Guards:
  - JwtGuard.
- Middleware:
  - SessionMiddleware;
- Repository:
  - AppSettingsRepository.
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
