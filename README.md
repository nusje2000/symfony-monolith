# Symfony monolith

An example of a monolithic repository with multiple standalone applications.

The current code shoulf *NOT* be used in a production environment.

## Motivation

At the company I work for, we use a monolithic repository that contains multiple symfony applications. When a deployment happens, the entire repository will be
packaged and deployed to the server, including all the code that the application does not use. All the applications in this repository are also tightly coupled
to the existance of the monolithic repository and are not able to run on themselves.

## The structure

The `src` directory contains a folder named Application, within this folder, multiple applcations can be placed. Each application provides its own binaries,
kernel, assets, etc. This is done because when deploying your application, you only want to deploy a single application.

When developing the applications, a single server can host all the applications. To select the application, you can use a subdomain (like admin.localhost or
api.localhost). This routing will be done by the index that is provided by the monolithic repository. Because the index in the root routes request via the
correct kernel, there is almost no code/config required in the applications themselves, keeping the applications decoupled from the monolithic repository.

There are a few changes that conflict with the symfony directory structure. These include:

1. All directories that do not contain code will be placed under Resources, these include: `public`, `config`, `templates`, etc.

## Deploying to production

TODO

## Utilities

TODO

## TODO

### DevOps

- package command (for creating releases)

### Frontend

- encore

### Development utilities

- ui for managing all applications
- root index page
- adding a simple configuration inferface (like yml, xml, etc.)

# The development process

If you are intrested in the development process of this repository, you can check out the [Dev Log](./docs/dev_log.md) for issue's I've encountered and the
solutions to them.
