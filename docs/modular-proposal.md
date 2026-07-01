# Modular Organization Proposal

## Problem

Currently everything is flat:
```
app/Models/          ← 33 models in one folder
app/Services/        ← 33 services + 33 interfaces mixed
app/Controllers/Api/ ← 33 controllers
app/Repositories/    ← 66 files (interfaces + implementations)
```

As we add more features, this becomes unwieldy.

## Proposed Structure

Group files by **domain module** so each module is self-contained:

```
app/Modules/
  Shared/                         ← shared base classes
    Controller.php
    BaseService.php
    BaseRepository.php

  Auth/                           ← Module 1
    Models/User.php, Role.php
    Controllers/AuthController.php, UserController.php
    Services/UserService.php
    Repositories/UserRepository.php
    Requests/LoginRequest.php, UserRequest.php
    Resources/UserResource.php, UserCollection.php
    Providers/AuthServiceProvider.php
    routes.php

  Academic/                       ← Module 2
    Models/AcademicYear.php, Term.php, ClassLevel.php, Stream.php
    Controllers/
    Services/
    Repositories/
    Requests/
    Resources/
    Providers/AcademicServiceProvider.php
    routes.php

  Students/                       ← Module 4
    Models/Student.php, Guardian.php, StudentGuardian.php, Enrollment.php
    Controllers/
    Services/
    Repositories/
    Requests/
    Resources/
    Providers/StudentServiceProvider.php
    routes.php

  Curriculum/                     ← Module 5
    ... (subjects, themes, outcomes, skills)

  Assessment/                     ← Module 6
    ... (types, grading, records, results)

  Attendance/                     ← Module 8
  Timetable/                      ← Module 9
  Finance/                        ← Module 10
  Discipline/                     ← Module 11
  Library/                        ← Module 12
  Communication/                  ← Module 13
  Reports/                        ← Module 7
```

## How It Works

1. **Each module owns its files** — Models, Controllers, Services, Repositories, Requests, Resources all live in the module folder.

2. **Service Provider per module** — One provider per module registers all its bindings. `bootstrap/providers.php` only lists module providers.

3. **Module routes** — Each module has its own `routes.php` included from the main `routes/api.php`.

4. **Auto-loaded via PSR-4** — Add to `composer.json`:
```json
"autoload": {
    "psr-4": {
        "App\\Modules\\": "app/Modules/"
    }
}
```

5. **Preserves SOLID** — Interfaces stay alongside implementations within the module.

## Module Scaffold Script

A `php artisan make:module` script that creates:

```
make:module {name}
  Creates:
    app/Modules/{Name}/
      Models/
      Controllers/
      Services/
        Contracts/
      Repositories/
        Contracts/
      Requests/
      Resources/
      Providers/{Name}ServiceProvider.php
      routes.php
```

## Migration Path

Rather than rewriting the entire codebase at once, migrate one module at a time:
1. Create module structure
2. Move files into module
3. Update namespaces
4. Update routes/api.php to include module routes
5. Update bootstrap/providers.php
6. Run tests
