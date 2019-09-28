# Provision Ops: Update Dependencies

This Composer plugin will react to `composer update` by creating a new git 
branch and pull request automatically.

It is being designed to be fully automatable for continuous delivery 
of updates for any composer package.

## Usage


    composer require provision-ops/update-dependencies
    
## WIP

More information coming soon. This project is brand new.

## Workflow

1. `post-package-update` composer hook fires after user or CI calls `composer update`.
2. A new branch is created based on the package being updated. 
3. Changes to `composer.lock` are committed.
4. New branch is pushed.
5. New Pull Request is created, if GITHUB_TOKEN is available.
6. If tests pass and commit status is good, automatically merge the PR
   (Stretch Goal. Not sure if this tool can handle this)