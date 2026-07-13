# [[ .ProjectName ]]

[[ .ProjectDescription ]]

## Local setup

### Requirements

- [Docker](https://www.docker.com/products/docker-desktop) (macOS users should prefer
  [OrbStack](https://orbstack.dev/download) for better DX)
- [Task](https://taskfile.dev/installation/)

> [!NOTE]
> With the above requirements installed you should be able to start your local development environment pretty easily on
> Linux and MacOS. Windows users can follow the instructions in
> [Windows Local Development Setup](docs/windows-local-development-setup.md).

### Task

[Task](https://taskfile.dev/) is a task runner / build tool. It is a simple tool that allows you to define and run tasks
for your projects. It is similar to `make` but cross-platform and written in Go. To retrieve a complete list of all
tasks run `task --list` in your terminal. Here is a short list of tasks to get you started:

#### Starting the project

To get the project up and running, run `task up`. This will automate all the steps needed to get the project up and
running.

| Description       | Uri or host:port        | OrbStack domain            |
|-------------------|-------------------------|----------------------------|
| Application url   | <http://localhost:8080> | <https://[[ .ProjectShortName | toKebabCase ]].local>      |
| Mailpit url       | <http://localhost:8025> | <https://mail.[[ .ProjectShortName | toKebabCase ]].local> |
| [[ .DbLabel ]] host port | `localhost:[[ .DockerDbPort ]]`        | `[[ .DbOrbStackDomain ]]`          |

> [!TIP]
> If you are a macOS user and you are using OrbStack, you can navigate to <http://orb.local> to see the domains of all
> your running containers.

#### Stopping the project

To stop the project, run `task stop`. This won't delete the projects containers and volumes, so you can start the
project again with the same state later.

#### Stopping and cleaning up the containers and volumes

To stop and remove the project's containers and volumes, run `task down`. You database state will be lost.

#### Resetting the project

To reset the project, run `task reset`. This will stop the project and remove all containers and volumes. And finally
start the project up again.

#### Refreshing the application

To refresh the project when for instance you are switching to a different branch, run `task app:refresh`. This will for
instance (re)fetch the composer and npm dependencies, run the migrations and rebuild the front-end assets

#### Working with git worktrees

You can run several branches at the same time, each with its own containers, volumes and
dependencies, using git worktrees. Every worktree gets a unique `COMPOSE_PROJECT_NAME`, so its
containers, network, volumes and (on OrbStack) its `*.local` domains never collide with the main
project or other worktrees.

- `task worktrees:create NAME=my-feature` — create a worktree (new branch `my-feature`) in a
  sibling directory `../[[ .ProjectShortName | toKebabCase ]]-worktrees/my-feature`, generate an
  isolated `.env`, and bring its stack up. Pass `BRANCH=existing-branch` to check out an existing
  branch instead. On OrbStack the app is served at `https://[[ .ProjectShortName | toKebabCase ]]-my-feature.local`;
  without OrbStack the task prints the unique host ports it assigned.
- `task worktrees:list` — list all worktrees.
- `task worktrees:delete NAME=my-feature` — tear down the worktree's containers/volumes and remove
  its directory (the git branch is kept; delete it with `git branch -d my-feature`).

#### Using Clockwork to debug the application

Checkout <https://[[ .ProjectShortName | toKebabCase ]].local/clockwork> to see the [clockwork](https://underground.works/clockwork/) dashboard.
[[- if .AddE2E ]]

### End-to-end testing (Playwright)

End-to-end tests live in the `e2e/` directory and run with [Playwright](https://playwright.dev/).

To run them on your host, first install the browsers once with `task e2e:setup`, then:

- `task e2e:test` — run the tests in the Playwright UI
- `task e2e:test:headless` — run the tests headless
- `task e2e:codegen` — record a test by interacting with the app
- `task e2e:show-report` — open the last HTML report

To run the suite in Docker against the running stack (as CI does), use `task e2e:docker:test`.

A deterministic fixture user is available via `Database\Seeders\E2ETestSeeder`; seed it
with `task artisan:run:db:seed -- --class='Database\Seeders\E2ETestSeeder'`.
[[- end ]]
