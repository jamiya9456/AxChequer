## Apache Access Checker

This is a tiny-simple PHP script and HTML form for testing <code>.htaccess</code> 
files. (Code name: <code>The AxChequer</code>.)

### Installation

By default it likes to be in:

    <htdocs>/ax/

with the access file to check in:

    <htdocs>/ax/check/

That setup isolates the testing from itself by using <code>fsockopen</code> on 
<code>http://localhost/ax/check/</code>.

The directory structure:

    ax/index.php
    ax/index.html
    ax/htaccess-examples.txt
    ax/check/.htaccess
    ax/check/index.php
    ax/check/index.html
    ax/check/foo.txt
    ax/check/foo.js
    ax/check/foo/index.html
    ax/check/foo/foo.txt

(The use of the foo files will become clear when testing.)

### Usage

Loading <code>http://localhost/ax/[index.php]</code> presents an HTML form with 
inputs for:

    url:
    referer:
    agent:

The default URL will be <code>http://localhost/ax/check/</code>

In addition there is a list of all the example access files from which to 
choose to copy into <code>check/</code> for testing &ndash; or copy in your 
own access file to test.

Then add to the URL or REFERER/AGENT and submit the form to check results.

(If only somehow IP addresses could be varied...)

### Oh, Yeah

The reason for this is two: 1) In trying to keep up with Bots, access files 
can grow large and get complicated; 2) Modifying the remote server and waiting 
for the Bot(s) to come back to see the result(s) is rather dull.

This code helps by allowing one to try to optimise access files for efficiency 
by experimentation, and to be able to simulate Bots (by their signature &ndash; 
99% of all Bots have signatures) and the files they look for.

The added benefit of <code>The AxChequer</code> is for *learning Apache* 
*configuration and Directives*. (A terminal with tail for seeing the error log, 
an editor editing <code>.htaccess</code> and <code>http://localhost/ax/</code> 
in a Browser.)

#### About Apache Configuration

*Editorializing...*

Apache's access file format has been called "one of the most powerful 
configuration files you will ever come across", yet, probably due to features 
added over the many years of update, many Directives are inconsistent in 
form and usage; some Directives cannot be used in <code>.htaccess</code>; 
FilesMatch can, but DirectoryMatch cannot &ndash; and the latter would be very 
useful indeed.

In addition there are no string operations such as sub string and concatenation 
which would be useful for forming environment variables &ndash; adding more 
expressions would be nice.

Some Directives use <code>%{env:ENVAR}</code> and others <code>%{ENVAR}e</code> 
and there are too few examples of both.

There's more, but this is not to take anything away from Apache &ndash; it will 
remain the most excellent HTTP server of all time.

### One More Thing

I'd like to encourage people to be generous in submitting issues and feedback.

I still have much to learn about Apache configuration.
