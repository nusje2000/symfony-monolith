# Symfony monolith

An example of a monolithic reposotory with multiple standalone applications.

## The structure

The `src` directory contains a folder named Application, within this folder, multiple applcations can be placed. Each application provides its own binaries,
kernel, assets, etc. This is done because when deploying your application, you only want to deploy a single application.

When developing the applications, a single server can host all the applications. To select the application, you can use a subdomain (like admin.localhost or
api.localhost). This routing will be done by the index that is provided by the monolithic repository. Because the index in the root routes request via the
correct kernel, there is almost no code/config required in the applications themself, keeping the applications decoupled from the monolithic repository.

## Encountered issues and the solutions

### Correct directory settings

Directories like the cache and logging cannot be shared since these are applcation specific. Both the cache and logging should be stored in seperate
directories, but still be contained in a single `var` directory.

#### Solution 1

Using environment variables to add the ability to set the logging and cache directory from anywhere in the application. When these are not defined, then the
fallback can be used.

## Deploying to production

TODO

## Utilities

TODO

## TODO

### Routing

- root console.php

### DevOps

- composer monolith
- CI
- phpstan
- psalm
- package command (for creating releases)

### Frontend

- assets

### Development utilities

- cache clearing
- cross-application commands
- ui for managing all applications
- root index page
- adding a simple configuration inferface (like yml, xml, etc.)
