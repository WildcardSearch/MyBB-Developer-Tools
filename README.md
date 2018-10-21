# MyBB-Developer-Tools 0.1 Beta
*a MyBB module for developers with a collection of useful tools that is expansible and extensible*

**This plugin is a development tool and therefore should not be used carelessly. Do not use this plugin on a live forum unless you understand the risks and are prepared to deal with the consequences.**

## Features
### PHiddle
The main module tab is a PHP fiddle (PHiddle) that can be used to easily run scripts on the board. The MyBB core libraries are automagically loaded into the final script so you can focus on your code.

This tool and can be used to easily develop scripts to test read queries and format output; correct database snafus on a live forum (after testing and ensuring the outcome, of course); or just trying a section of code independently to ensure your knowledge (of a function or class, for example) is correct.

PHiddles can be saved, loaded, deleted, exported, and imported.

### Modules
This plugin is modular and developers and hobbyists can easily develop unique tools or customize existing tools for their preferences/dev environment.

#### Create Users
This module allows many options for creation of users and can create hundreds of fake users at a time with random user names pulled from an international repository for first and last names.

#### Create Threads
This module can be used to create threads on the forum complete with as many posts per thread as needed. The thread titles and post contents are randomly concatated Lorem Ipsum (generate by [php-loremipsum](https://github.com/joshtronic/php-loremipsum)).

This module features pagination for over one thousand posts to (hopefully avoid PHP timeouts).

#### Create User Name Avatars
This module creates PNG avatars for each user with their username drawn on the avatar itself. This can be useful when developing/testing plugins that deal with lists of user avatars.

### Permissions
Uses the ACP permissions system. Owner can block an admin from the entire plugin or from a single module.

### Extensibility
This plugin uses my ExternalModule interface to provide easy extension through external modules. Look for a wiki link here when we reach beta.

## Dependencies

### Packaged
[php-loremipsum](https://github.com/joshtronic/php-loremipsum)

### Required
none
