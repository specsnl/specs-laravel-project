# specs-laravel-project

A [Specs](https://github.com/specsnl/specs-cli) template for PHP Laravel 13 projects.

## Install/Usage instructions

**Requirements:**
- [Specs CLI](https://github.com/specsnl/specs-cli)

Download the template and store it locally:

```bash
specs template download github:specsnl/specs-laravel-project laravel-project
```

Or use it directly without downloading:

```bash
specs use github:specsnl/specs-laravel-project <dir-name>
```

## Overview task commands

An overview of all Task commands:

```bash
$ task --list

task: Available tasks for this project:
* cleanup:                Cleanup of almost all git gitignored files and untracked files
* test:                   Test the specs template
* update:                 Update the specs template
* cleanup:dry-run:        Display all files that are either ignored or untracked
* test:cleanup:           Cleanup specs template test files
* test:interactive:       Test the specs template interactivly
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
