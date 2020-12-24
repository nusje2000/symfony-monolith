# Encountered issues and the solutions

## Correct directory settings

Directories like the cache and logging cannot be shared since these are applcation specific. Both the cache and logging should be stored in seperate
directories, but still be contained in a single `var` directory.

### Solution

Using environment variables to add the ability to set the logging and cache directory from anywhere in the application. When these are not defined, then the
fallback can be used.

## Defining the application environment in a console command

When executing a command using the root bin/console you want to be able to specify the application that the command should be executed in.

### Solution

Add a custom implementation of the `Symfony\Bundle\FrameworkBundle\Console\Application` class, that defines --application as input option. Then
the `Acme\Component\SymfonyMonolith\Loader\ArgvLoadingStrategy` can use this option since the command will now be valid for the application.

## Application registry has to be redefined in each entry point

When defining a new entry point (like bin/console or public/index.php), you have to redefine the list of available applications.

### Solution

Add an ApplicationRegistryFactory to the root of the `src/Application` directory. Thic class can now contains the logic for creating the registry. 
