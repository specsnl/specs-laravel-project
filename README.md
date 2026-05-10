# specsnl/specs-laravel-project

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
* test:                   Test the boilr template
* update:                 Update the boilr template
* cleanup:dry-run:        Display all files that are either ignored or untracked
* test:cleanup:           Cleanup boilr template test files
* test:interactive:       Test the boilr template interactivly
```

## Links

- VSCode [Sharing Git/GPG Credentials](https://code.visualstudio.com/remote/advancedcontainers/sharing-git-credentials)
  with DevContainer

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
