# cmsms-CGCalendar

Maintained fork of the **CGCalendar** module for CMS Made Simple.

Calguy's calendar and event-management module, including event categories, custom fields, recurrence, front-end calendar views, RSS feeds, and iCalendar exports.

This repository is a community-maintained fork of CGCalendar for [CMS Made Simple](https://www.cmsmadesimple.org) (CMSMS), originally written by Robert Campbell (*calguy1000*).

## Why this fork exists

The original author left the CMSMS project and the CG ("Calguy") modules will not receive further official releases. Many sites still depend on them, while the last releases predate PHP 8.

Rather than patching the copy inside every site separately, fixes live here so every site can use the same tested code.

## Compatibility

| | |
|---|---|
| Based on | CGCalendar **2.6.2** (final upstream release) |
| Tested with | CMS Made Simple 2.2.24, Smarty 4.5.5, **PHP 8.5.12**, AlmaLinux 9 / Apache 2.4 |
| Minimum CMSMS | 2.2.8 (unchanged from upstream) |
| Requires | **CGExtensions** >= 1.61 and **CGSimpleSmarty** >= 2.1.6. |

## Version number

The fork reports version **2.6.3**, a patch release above the final upstream 2.6.2, so Module Manager shows that the fork is installed. After replacing or updating the module, run the upgrade offered in Module Manager. CMSMS will not load the module until it has run. This release has no schema or data migrations.

## Installing / updating a site

The repository is called `cmsms-CGCalendar`, but the module folder inside a site must be named exactly **`CGCalendar`**.

```sh
# Replace an existing module with the fork.
cd /path/to/site/modules
mv CGCalendar CGCalendar.orig
git clone https://github.com/stom66/cmsms-CGCalendar.git CGCalendar

# Later updates.
cd /path/to/site/modules/CGCalendar && git pull --ff-only
```

Clear the CMSMS cache after replacing or updating the module. Ensure the web server blocks access to `.git` directories.

## Changes in this fork

The first commit is the upstream release. Later commits contain one fix each:

- fix(CGCalendar): use `continue 2` when an unavailable custom-field dependency should skip the enclosing field loop on PHP 8.

## Contributing

- One fix per commit, using [Conventional Commits](https://www.conventionalcommits.org).
- Keep behaviour and rendered output unchanged unless the change is the fix.
- Test against the PHP versions in the compatibility table. `php -l` alone is not enough for runtime compatibility.
- Do not change the module version without an upgrade path.

## Licence

GNU General Public License, version 2 or later, with the upstream author's additional condition in the source-file headers.

Copyright © Robert Campbell (calguy1000) and contributors. The full licence text is in [`doc/COPYING`](doc/COPYING).
