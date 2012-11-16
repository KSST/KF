# Contributing

## Initial Setup

At first you need to setup your contributor repository with an upstream to the main repository.
Here is what you need to do:

 1. Setup a GitHub account (http://github.com/), if you haven't yet
 2. Fork the Koch Framework respository (http://github.com/KSST/KF/)
 3. Clone your fork locally and enter it (use your own GitHub username
    in the statement below)

    ```sh
    git clone http://github.com/<username>/KF.git
    cd KF
    ```

 4. Add a remote upstream to the canonical Koch Framework repository, so you can keep your fork
    up-to-date:

    ```sh
    git remote add upstream http://github.com/KSST/KF.git
    ```

 5. Fetch and merge the latest remote changes in your local branch

    ```sh
    git pull upstream develop
    ```

## Working on Koch Framework

Please do each new feature or bugfix in a new branch.
This simplifies code reviewing, keeping up-to-date as well as merging your changes into the main repository.

A typical workflow consists of the following steps:

 1. Create a new local branch based off your develop branch.
 2. Switch to your new local branch.

    (This step can be combined with the previous step with the use of)

    ```sh
    git checkout -b <branchname>
    ```

 3. Do some work, commit, repeat as necessary.
 4. Push the local branch to your remote repository.

    ```sh
    git push origin <branchname>:<branchname>
    ```
 5. Send a pull request.

 6. Select proper target branch.
    Pull Requests for bug fixes should be made against the current release branch (hotfix-1 => release branch x.y).
    Pull Requests with new features should be made against master (feature-1 => master).
    Pull Requests with backward compatibility breaking changes should be made against master (bcb-hotfix-1 => master).

## Keeping Up-to-Date

Periodically, you should update your fork or personal repository to match the canonical Koch Framework repository.
In the above setup, we have added a remote to the Koch Framework repository, which allows you to do the following.

```sh
git checkout develop
git pull upstream develop
- OPTIONALLY, to keep your remote up-to-date -
git push origin develop:develop
```
