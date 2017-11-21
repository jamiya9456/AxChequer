## Apache Access Checker

This is a tiny-simple PHP script and HTML form for testing `.htaccess` files. 
Code name: `The AxChequer`. (This is hasty code, not meant to be pretty. It 
just needs to work.)

See related: [PHP Access Blocker](https://github.com/AndovaBegarin/AxBlock). Code to block or 
flag requests based on regular expressions or string matches of server data, 
and/or comment spam.

### AxChequer Installation

By default it likes to be in:

    <htdocs>/ax/

with the access file to check in:

    <htdocs>/ax/check/

That setup isolates the testing from itself by using `fsockopen` on 
`http://localhost/ax/check/` or any other URL (via an input).

The directory structure:

    ax/index.php
    ax/README.md
    ax/htaccess-examples.txt
    ax/check/.htaccess
    ax/check/index.php
    ax/check/index.html
    ax/check/foo.txt
    ax/check/foo.js
    ax/check/foo/index.html
    ax/check/foo/foo.txt
    
(The use of the foo files are for testing.)

#### Example Access Files

The access files test just a few directives at a time and have comments 
about them were appropriate/useful. Some of them use 2.4+ directives.

The main point of these examples are for *stopping Bots looking for exploits* 
(and annoying robots and other pests), trying to stop them early and to 
minimize the data sent for them.

(They are sloppy/incomplete as they are just for testing, not the real world.)

#### Usage

Loading `http://localhost/ax/[index.php]` presents an HTML form with 
inputs for:

    url:
    referer:
    agent:

The default URL will be `http://localhost/ax/check/` but can be changed to 
anything.

In addition there is a list of all the example access files from which to 
choose to copy into `check/` for testing &ndash; or copy in your own access 
file to test.

Then add to the URL or REFERER/AGENT and submit the form and check results.

(If only somehow IP addresses could be varied...)

#### Oh, Yeah

The reason for this is:

1. In trying to keep up with Bots, access files can grow large and get complicated.
2. Modifying a remote server and waiting for the Bot(s) to come back to see the result(s) is rather dull.
3. Optimising access files and eliminating Rewrite rules when other directives will do.
4. It's useful for learning Apache configuration and Directives.

From "When not to use mod_rewrite":

>mod_rewrite should be considered a last resort, when other alternatives are 
>found wanting. Using it when there are simpler alternatives leads to 
>configurations which are confusing, fragile, and hard to maintain. 
>Understanding what other alternatives are available is a very important step 
>towards mod_rewrite mastery.
>
>Note that many of these examples won't work unchanged in your particular server 
>configuration, so it's important that you understand them, rather than merely 
>cutting and pasting the examples into your configuration.

This code helps by allowing one to try to optimise access files for efficiency 
by experimentation, and to be able to simulate Bots (by their signature &ndash; 
99% of all Bots have signatures) and the files they look for.

### Change Log

#### November 21, 2017

* Removed "AxBlock"; no other changes.

#### v2.0.2

* Added `axblock.php` and associated files.
* Improved HTML output when checking remote URLs.

#### v2.0.0

* Slight improvements to `index.php`.
* Slight improvements to this file.
* Slight improvements to the default `.htaccess`.
