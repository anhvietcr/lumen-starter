## Lumen Starter Template [![Build Status](https://travis-ci.org/realpage/lumen-starter.svg?branch=master)](https://travis-ci.org/realpage/lumen-starter)

This [Laravel Lumen](https://lumen.laravel.com) starter template is intended to be used for new projects.

## README Contents

* [What's Included](#whats-included)
* [Requirements](#requirements)
* [Using This Repository](#using-this-repo)
* [Testing](#testing)
* Continuous Integration/Deployment Workflow Diagram - [Diagram](http://realpage.github.io/devops-documentation/foundation-deployment-technical-v1.png) - [Video](https://www.youtube.com/watch?v=vHpInByhQfM)
* [FAQ](#faq)

> For details on how to contribute to this repo, please check out the [contributing guide](https://github.com/realpage/lumen-starter/blob/master/CONTRIBUTING.md).

<a name="whats-included" />

### What's Included

 * Latest version of Lumen.
 * Pre-configured `docker-compose.yml` that uses nginx, php-fpm and PostgreSQL. ([How can I use MySQL?](#use-mysql))
 * [Travis-CI](https://travis-ci.org) integration:
    * Checks [psr-2 compliance](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) with [phpcs](https://github.com/squizlabs/PHP_CodeSniffer).
    * Runs [phpunit](https://phpunit.de/) tests within docker containers.
    * Pushes deploy-ready containers for `staging` and `master` branches to [DockerHub](http://hub.docker.com).
 * [Dredd support](https://github.com/apiaryio/dredd) for ensuring accurate API documentation.
 * [Environment reset script](https://github.com/realpage/lumen-starter/blob/master/infrastructure/reset_environment.php) to allow a dev to easily reset a seeded environment
 * [Example configuration](infrastructure/rancher-example/README.md) for deploying to [Rancher](http://rancher.com).

<a name="using-this-repo" />

### Using This Repository

We recommend [watching this repository](https://help.github.com/articles/watching-repositories/) so you can apply updates made to this project to yours.

_Realpage teams should contact **foundation-devops@realpage.com** using [this email template](https://github.com/realpage/lumen-starter/wiki) to get everything setup._

For everyone else:

1. Clone this repo and delete the `.git` directory
2. Run `git init` and [change the origin of the repo](https://help.github.com/articles/changing-a-remote-s-url/) to point to your remote repository
3. Reference the [contributing guide](https://github.com/realpage/lumen/blob/master/CONTRIBUTING.md) for running this application locally
4. After running locally `docker exec -it $(docker ps -f name=fpm -q) php artisan clean:template` to strip out example migrations, seeds, tests, etc...

#### Dockerhub Setup

Create a repository that mirrors your GitHub namespace/repository except does not include dashes (DockerHub doesn't allow dashes).  The travis configuration is already designed to handle this transition for you.  An example of this would be:

GitHub: my-namespace/my-new-project

DockerHub: mynamespace/mynewproject

#### Travis-CI Setup

Configure the following environment variables:
 * `DOCKER_USERNAME`
 * `DOCKER_PASSWORD`

This user needs to have permission to write to the DockerHub repository so that it can push images.

<a name="testing" />

### Testing

 * [Dredd support](https://github.com/apiaryio/dredd) for ensuring accurate API documentation.  Can also be considered as acceptance tests
 * PHPUnit for unit/functional testing

For more on testing, please see: [Testing in the Lumen Starter](https://www.youtube.com/watch?v=BPX792GtcbE) on YouTube.

<a name="faq" />

### FAQ

<a name="use-mysql" />

##### **How can I use MySQL?**
   * Set the `DB_CONNECTION` environment variable to `mysql`
   * Update the fpm/cli docker containers to `apt-get install php7.0-mysql`

##### **How do I update my nginx config?**
   * the `default.conf` file located in the `infrastructure/nginx` directory will be added to the nginx container as part of the build
   * update the file and rebuild the container via `docker-compose build` to propagate the changes
