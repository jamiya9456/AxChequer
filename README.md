## Apache Access Checker / PHP Access Blocker

This is a tiny-simple PHP script and HTML form for testing `.htaccess` files. 
Code name: `The AxChequer`. (This is hasty code, not meant to be pretty. It 
just needs to work.)

Now includes a PHP Access Blocker, code name: `AxBloquer`. Code to block or 
flag requests based on regular expressions or string matches of server data, 
and/or comment spam.

>Update: The Access Blocker code now has it's own repository: 
>[AxBlock](https://github.com/AndovaBegarin/AxBlock).

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

### Usage

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

### Oh, Yeah

The reason for this is:

1. In trying to keep up with Bots, access files can grow large and get complicated.
2. Modifying a remote server and waiting for the Bot(s) to come back to see the result(s) is rather dull.
3. Optimising access files and eliminating Rewrite rules when other directives will do.

From "When *not* to use mod_rewrite":

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

The added benefit of `The AxChequer` is for *learning Apache configuration and Directives*.

### AxBloquer Installation

The Access Block code is `axblock.php` in the same location, and `index.php` 
can optionally call it. This code is fully independent and can be incorporated 
into an existing (PHP) website.

There are several support files:

    axblock.ini             configuration

    agents.txt              user-agent regex matches
    referers.txt            referer regex matches
    ips.txt                 remote-addr substring matches
    queries.txt             query-string regex matches

    banwords.txt            comment ban substring/regex matches
    modwords.txt            comment moderate words

The INI file explains:

    ; These are the "how to deal with error" strings:
    ;
    ; 1) function name
    ; 2) <4xx code> [filename|string]
    ;
    ; If function name it must handle everything (example provided for user-agent).
    ; If <4xx code> that status code gets sent; if [filename] that file follows 
    ; the header (if a .txt file a plain/text header is sent); if [string] the 
    ; string follow the header.

    bad_user_agent = bad_user_agent
    bad_referer = 404 badreferer.txt
    bad_remote_addr = 403 access denied
    bad_query = 404

    ; The strings to block - all are regular expressions except for ips which 
    ; are substrings to match (i.e. '4.4.4.' will match IP '4.4.4.5').

    ips = ips.txt
    agents = agents.txt
    referers = referers.txt
    queries = queries.txt

    ; Comment Spam Detection
    ;
    ; Currently, just one POST id is used (comment_post_id), and if not empty 
    ; will first be matched with banwords.txt list, which can either be strings 
    ; or regular expressions - the default list is to reject links and/or email 
    ; addresses (the majority of comment spam). modwords.txt are words that some 
    ; spammers use to "seed" comments with brand or product words/phrases like 
    ; "cosmetic", "lipstick" etc. for search engines to find.
    ;
    ; For these, the code does not "block" the post but will either call the
    ; function bad_comment_word() and/or mod_comment_word() if defined, or
    ; create constants 'AXBLOCK_COMMENT_BAN' to the first banned word, and/or 
    ; 'AXBLOCK_COMMENT_MOD' to the word(s) flagged for moderation (matching a 
    ; count).

    comment_post_id = comment
    banwords = banwords.txt
    modwords = modwords.txt
    mod_words_count = 3

The Access Blocker code is 200 lines long. It is fully customizable as there 
are no string literals for blocking in the code &ndash; all "user strings" are 
in external, plain text files, one match per line.

### Change Log

#### v2.0.2

* Added `axblock.php` and associated files.
* Improved HTML output when checking remote URLs.

#### v2.0.0

* Slight improvements to `index.php`.
* Slight improvements to this file.
* Slight improvements to the default `.htaccess`.
