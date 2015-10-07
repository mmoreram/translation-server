Translation Server
==================

This Translation Server aims to provide a very easy, intuitive and fast way of
adding new translations in your projects, following the Symfony Standards, and
using the console as interface.

## Tags

* Use last unstable version ( alias of `dev-master` ) to stay in last commit
* Use last stable version tag to stay in a stable release.
* [![Latest Unstable Version](https://poser.pugx.org/mmoreram/translation-server/v/unstable.png)](https://packagist.org/packages/mmoreram/translation-server)
[![Latest Stable Version](https://poser.pugx.org/mmoreram/translation-server/v/stable.png)](https://packagist.org/packages/mmoreram/translation-server)

## Install

Install Translation Server in this way:

``` bash
$ composer global require mmoreram/translation-server=dev-master
```

If it is the first time you globally install a dependency then make sure
you include `~/.composer/vendor/bin` in $PATH as shown [here](http://getcomposer.org/doc/03-cli.md#global).

### Always keep your Translation Server installation up to date:

``` bash
$ composer global update mmoreram/translation-server
```

### .phar file

You can also use already last built `.phar`.

``` bash
$ git clone git@github.com:mmoreram/translation-server.git
$ cd translation-server
$ php build/translation-server.phar
```

You can copy the `.phar` file as a global script

``` bash
$ cp build/translation-server.phar /usr/local/bin/translation-server
```

### Compile

Finally you can also compile your own version of the package. ( You need set `phar.readonly = Off` in your php.ini ).

``` bash
$ git clone git@github.com:mmoreram/translation-server.git
$ cd translation-server
$ composer update
$ php bin/compile
$ sudo chmod +x build/translation-server.phar
$ build/translation-server.phar
```

You can copy the `.phar` file as a global script

``` bash
$ cp build/translation-server.phar /usr/local/bin/translation-server
```

## Config

If your project wants to provide support for this project, make sure you place
the definition about where are your translations, your master language and what
languages do you support in a file called `.translation.yml`.

This file has this format (no needs to explain it, right? ^^)

``` yml
master_language: en
languages:
    - en
    - es
    - ca
    - fr
    - de
    - it
    - fi
    - eu
    - gl
    - eo
    - nl
paths:
    - src/Folder/*/Resources/translations

```

you can also define where to search the `.translation.yml` file using the
`--config|-c` option

``` bash
$ translation-server translation:server:view --config="src/"
```

## Commands

This server provides a set of commands useful for your project. Let's see all
these commands one by one.

``` bash
Console Tool

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message.
  --quiet          -q Do not output any message.
  --verbose        -v|vv|vvv Increase the verbosity of messages
  --version        -V Display this application version.
  --ansi              Force ANSI output.
  --no-ansi           Disable ANSI output.
  --no-interaction -n Do not ask any interactive question.

Available commands:
  help                   Displays help for a command
  list                   Lists commands
translation
  translation:server:add   Add new translation
  translation:server:sort  Sort translations
  translation:server:view  View statics about the server
```

### Translations statics

You can see all your translation statics by using this command. Without any
extra configuration, you will be able to see statics of all the project.

``` bash
$ translation-server translation:server:view

[Trans Server] Command started at Thu, 08 Oct 2015 00:52:41 +0200
[Trans Server] Translations for [en] is 100% completed. 0 missing
[Trans Server] Translations for [ca] is 99.57% completed. 4 missing
[Trans Server] Translations for [es] is 99.57% completed. 4 missing
[Trans Server] Translations for [fr] is 78.34% completed. 203 missing
[Trans Server] Translations for [de] is 68.84% completed. 292 missing
[Trans Server] Translations for [it] is 0.11% completed. 936 missing
[Trans Server] Translations for [nl] is 0% completed. 937 missing
[Trans Server] Translations for [eo] is 0% completed. 937 missing
[Trans Server] Translations for [gl] is 0% completed. 937 missing
[Trans Server] Translations for [eu] is 0% completed. 937 missing
[Trans Server] Translations for [fi] is 0% completed. 937 missing
[Trans Server] Command finished in 932 milliseconds
[Trans Server] Max memory used: 13893632 bytes
```

#### Filtering by language

You can filter all results by language using the option [--language|-l] in
all your commands. This is just a mask, so if you define one or more language,
will simply mask all results provided to you.

``` bash
$ translation-server translation:server:view --language es -l ca

[Trans Server] Command started at Thu, 08 Oct 2015 00:54:09 +0200
[Trans Server] Translations for [en] is 100% completed. 0 missing
[Trans Server] Translations for [ca] is 99.57% completed. 4 missing
[Trans Server] Translations for [es] is 99.57% completed. 4 missing
[Trans Server] Command finished in 917 milliseconds
[Trans Server] Max memory used: 13893632 bytes
```

As you can see, you can provide several languages

#### Filtering by domain

You can filter all results by domain using the option [--domain|-d] in
all your commands. This is just a mask, so if you define one or more domains,
will simply mask all results provided to you.

``` bash
$ translation-server translation:server:view --domain routes

[Trans Server] Command started at Thu, 08 Oct 2015 00:56:16 +0200
[Trans Server] Translations for [de] is 100% completed. 0 missing
[Trans Server] Translations for [en] is 100% completed. 0 missing
[Trans Server] Translations for [ca] is 100% completed. 0 missing
[Trans Server] Translations for [es] is 100% completed. 0 missing
[Trans Server] Translations for [eo] is 0% completed. 38 missing
[Trans Server] Translations for [nl] is 0% completed. 38 missing
[Trans Server] Translations for [gl] is 0% completed. 38 missing
[Trans Server] Translations for [it] is 0% completed. 38 missing
[Trans Server] Translations for [fr] is 0% completed. 38 missing
[Trans Server] Translations for [fi] is 0% completed. 38 missing
[Trans Server] Translations for [eu] is 0% completed. 38 missing
[Trans Server] Command finished in 842 milliseconds
[Trans Server] Max memory used: 13369344 bytes
```

You can provide as well several domains, even mix languages and domains.

### Asking for new translations

The power of this tool is that, just telling what language you'd like to work
with, the tool will ask you interactively some translations.

Let's see an example. In that case we will ask some translations in Basque, but
we only want to add routing translations, marked with the domain `routes`.

``` bash
$ translation-server translation:server:add --language eu --domain routes

[Trans Server] Command started at Thu, 08 Oct 2015 00:59:43 +0200
[Trans Server] Language : eu
[Trans Server] Key : store_cart_nav
[Trans Server] Original : /cart/nav
[Trans Server] Translation : []
```

At this point, the prompt will wait for your response here.
As soon as you have introduced your translation, just press *Enter* and your
translation will be stored in it's place.

This means that if a new file must be created in order to store your
translation, for example if you are creating a new language, the process will
create it.

``` bash
$ translation-server translation:server:add --language eu --domain routes

[Trans Server] Command started at Thu, 08 Oct 2015 00:59:43 +0200
[Trans Server] Language : eu
[Trans Server] Key : store_cart_nav
[Trans Server] Original : /cart/nav
[Trans Server] Translation : /saskia/nab

[Trans Server] Command started at Thu, 08 Oct 2015 01:00:25 +0200
[Trans Server] Language : eu
[Trans Server] Key : store_checkout_address
[Trans Server] Original : /cart/address
[Trans Server] Translation : []
```

As soon as the value is saved, the system will ask for another translation. As
long as you don't press *Ctrl+C*, the system will do it once and again.

### Sorting your translations

You can sort all your translations as well, just using this great command.

``` bash
$ translation-server translation:server:sort

[Trans Server] Command started at Thu, 08 Oct 2015 01:06:15 +0200
[Trans Server] Your translations have been sorted successfuly
[Trans Server] Command finished in 1288 milliseconds
[Trans Server] Max memory used: 13631488 bytes
```

Again, you can filter by language and domain.
