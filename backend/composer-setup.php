<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>Basic usage - Composer</title>
        <meta name="description" content="A Dependency Manager for PHP">
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <link rel="stylesheet" href="/build/app.css?v=2">
    </head>

    <body>
        <div id="container">
            <header>
                                    <a href="/"
                       title="Go back to the homepage"
                       aria-label="Go back to the homepage"><img src="/img/logo-composer-transparent.png" style="width:30px; margin-right: 10px" />Home</a><a class=""
                       href="/doc/00-intro.md"
                       title="Getting started with Composer"
                       aria-label="Getting started with Composer">Getting Started</a><a class=""
                       href="/download/"
                       title="Go the the Download page to see how to download Composer"
                       aria-label="Go the the Download page to see how to download Composer">Download</a><a class="active"
                       href="/doc/"
                       title="View the Composer documentation"
                       aria-label="View the Composer documentation">Documentation</a><a class="last"
                       href="https://packagist.org/"
                       title="Browse Composer packages on packagist.org (external link to Packagist.org)"
                       aria-label="Browse Composer packages on packagist.org (external link to Packagist.org)">Browse Packages</a>            </header>
            <main role="main">
                <div id="main">
                        <div id="searchbar" class="clearfix">
        <div id="docsearch"></div>
    </div>

            <ul class="toc">
                
                                                            <li>
                <a href="#introduction">Introduction</a> 
                            </li>
                                <li>
                <a href="#composer-json-project-setup">composer.json: Project setup</a> 
                                    <ul>
                            
                                                            <li>
                <a href="#the-require-key">The require key</a> 
                            </li>
                                <li>
                <a href="#package-names">Package names</a> 
                            </li>
                                <li>
                <a href="#package-version-constraints">Package version constraints</a> 
                            </li>
            
                    </ul>
                            </li>
                                <li>
                <a href="#installing-dependencies">Installing dependencies</a> 
                                    <ul>
                            
                                                            <li>
                <a href="#commit-your-composer-lock-file-to-version-control">Commit your composer.lock file to version control</a> 
                            </li>
                                <li>
                <a href="#installing-from-composer-lock">Installing from composer.lock</a> 
                            </li>
            
                    </ul>
                            </li>
                                <li>
                <a href="#updating-dependencies-to-their-latest-versions">Updating dependencies to their latest versions</a> 
                            </li>
                                <li>
                <a href="#packagist">Packagist</a> 
                            </li>
                                <li>
                <a href="#platform-packages">Platform packages</a> 
                            </li>
                                <li>
                <a href="#autoloading">Autoloading</a> 
                            </li>
            
        </ul>
    
    <h1 id="basic-usage">Basic usage<a href="#basic-usage" class="anchor">#</a></h1>
<h2 id="introduction">Introduction<a href="#introduction" class="anchor">#</a></h2>
<p>For our basic usage introduction, we will be installing <code>monolog/monolog</code>,
a logging library. If you have not yet installed Composer, refer to the
<a href="00-intro.md">Intro</a> chapter.</p>
<blockquote>
<p><strong>Note:</strong> for the sake of simplicity, this introduction will assume you
have performed a <a href="00-intro.md#locally">local</a> install of Composer.</p>
</blockquote>
<h2 id="composer-json-project-setup"><code>composer.json</code>: Project setup<a href="#composer-json-project-setup" class="anchor">#</a></h2>
<p>To start using Composer in your project, all you need is a <code>composer.json</code>
file. This file describes the dependencies of your project and may contain
other metadata as well. It typically should go in the top-most directory of
your project/VCS repository. You can technically run Composer anywhere but
if you want to publish a package to Packagist.org, it will have to be able
to find the file at the top of your VCS repository.</p>
<h3 id="the-require-key">The <code>require</code> key<a href="#the-require-key" class="anchor">#</a></h3>
<p>The first thing you specify in <code>composer.json</code> is the
<a href="04-schema.md#require"><code>require</code></a> key. You are telling Composer which
packages your project depends on.</p>
<pre><code class="language-javascript">{
    "require": {
        "monolog/monolog": "2.0.*"
    }
}</code></pre>
<p>As you can see, <a href="04-schema.md#require"><code>require</code></a> takes an object that maps
<strong>package names</strong> (e.g. <code>monolog/monolog</code>) to <strong>version constraints</strong> (e.g.
<code>1.0.*</code>).</p>
<p>Composer uses this information to search for the right set of files in package
"repositories" that you register using the <a href="04-schema.md#repositories"><code>repositories</code></a>
key, or in <a href="https://packagist.org">Packagist.org</a>, the default package repository.
In the above example, since no other repository has been registered in the
<code>composer.json</code> file, it is assumed that the <code>monolog/monolog</code> package is registered
on Packagist.org. (Read more <a href="#packagist">about Packagist</a>, and
<a href="05-repositories.md">about repositories</a>).</p>
<h3 id="package-names">Package names<a href="#package-names" class="anchor">#</a></h3>
<p>The package name consists of a vendor name and the project's name. Often these
will be identical - the vendor name only exists to prevent naming clashes. For
example, it would allow two different people to create a library named <code>json</code>.
One might be named <code>igorw/json</code> while the other might be <code>seldaek/json</code>.</p>
<p>Read more about <a href="02-libraries.md">publishing packages and package naming</a>.
(Note that you can also specify "platform packages" as dependencies, allowing
you to require certain versions of server software. See
<a href="#platform-packages">platform packages</a> below.)</p>
<h3 id="package-version-constraints">Package version constraints<a href="#package-version-constraints" class="anchor">#</a></h3>
<p>In our example, we are requesting the Monolog package with the version constraint
<a href="https://semver.madewithlove.com/?package=monolog%2Fmonolog&amp;constraint=2.0.*"><code>2.0.*</code></a>.
This means any version in the <code>2.0</code> development branch, or any version that is
greater than or equal to 2.0 and less than 2.1 (<code>&gt;=2.0 &lt;2.1</code>).</p>
<p>Please read <a href="articles/versions.md">versions</a> for more in-depth information on
versions, how versions relate to each other, and on version constraints.</p>
<blockquote>
<p><strong>How does Composer download the right files?</strong> When you specify a dependency in
<code>composer.json</code>, Composer first takes the name of the package that you have requested
and searches for it in any repositories that you have registered using the
<a href="04-schema.md#repositories"><code>repositories</code></a> key. If you have not registered
any extra repositories, or it does not find a package with that name in the
repositories you have specified, it falls back to Packagist.org (more <a href="#packagist">below</a>).</p>
<p>When Composer finds the right package, either in Packagist.org or in a repo you have specified,
it then uses the versioning features of the package's VCS (i.e., branches and tags)
to attempt to find the best match for the version constraint you have specified. Be sure to read
about versions and package resolution in the <a href="articles/versions.md">versions article</a>.</p>
<p><strong>Note:</strong> If you are trying to require a package but Composer throws an error
regarding package stability, the version you have specified may not meet your
default minimum stability requirements. By default, only stable releases are taken
into consideration when searching for valid package versions in your VCS.</p>
<p>You might run into this if you are trying to require dev, alpha, beta, or RC
versions of a package. Read more about stability flags and the <code>minimum-stability</code>
key on the <a href="04-schema.md">schema page</a>.</p>
</blockquote>
<h2 id="installing-dependencies">Installing dependencies<a href="#installing-dependencies" class="anchor">#</a></h2>
<p>To initially install the defined dependencies for your project, you should run the
<a href="03-cli.md#update-u"><code>update</code></a> command.</p>
<pre><code class="language-bashell">php composer.phar update</code></pre>
<p>This will make Composer do two things:</p>
<ul>
<li>It resolves all dependencies listed in your <code>composer.json</code> file and writes all of the
packages and their exact versions to the <code>composer.lock</code> file, locking the project to
those specific versions. You should commit the <code>composer.lock</code> file to your project repo
so that all people working on the project are locked to the same versions of dependencies
(more below). This is the main role of the <code>update</code> command.</li>
<li>It then implicitly runs the <a href="03-cli.md#install-i"><code>install</code></a> command. This will download
the dependencies' files into the <code>vendor</code> directory in your project. (The <code>vendor</code>
directory is the conventional location for all third-party code in a project). In our
example from above, you would end up with the Monolog source files in
<code>vendor/monolog/monolog/</code>. As Monolog has a dependency on <code>psr/log</code>, that package's files
can also be found inside <code>vendor/</code>.</li>
</ul>
<blockquote>
<p><strong>Tip:</strong> If you are using git for your project, you probably want to add
<code>vendor</code> in your <code>.gitignore</code>. You really don't want to add all of that
third-party code to your versioned repository.</p>
</blockquote>
<h3 id="commit-your-composer-lock-file-to-version-control">Commit your <code>composer.lock</code> file to version control<a href="#commit-your-composer-lock-file-to-version-control" class="anchor">#</a></h3>
<p>Committing this file to version control is important because it will cause anyone
who sets up the project to use the exact same
versions of the dependencies that you are using. Your CI server, production
machines, other developers in your team, everything and everyone runs on the
same dependencies, which mitigates the potential for bugs affecting only some
parts of the deployments. Even if you develop alone, in six months when
reinstalling the project you can feel confident that the dependencies installed are
still working, even if the dependencies have released many new versions since then.
(See note below about using the <code>update</code> command.)</p>
<blockquote>
<p><strong>Note:</strong> For libraries it is not necessary to commit the lock
file, see also: <a href="02-libraries.md#lock-file">Libraries - Lock file</a>.</p>
</blockquote>
<h3 id="installing-from-composer-lock">Installing from <code>composer.lock</code><a href="#installing-from-composer-lock" class="anchor">#</a></h3>
<p>If there is already a <code>composer.lock</code> file in the project folder, it means either
you ran the <code>update</code> command before, or someone else on the project ran the <code>update</code>
command and committed the <code>composer.lock</code> file to the project (which is good).</p>
<p>Either way, running <code>install</code> when a <code>composer.lock</code> file is present resolves and installs
all dependencies that you listed in <code>composer.json</code>, but Composer uses the exact versions listed
in <code>composer.lock</code> to ensure that the package versions are consistent for everyone
working on your project. As a result you will have all dependencies requested by your
<code>composer.json</code> file, but they may not all be at the very latest available versions
(some of the dependencies listed in the <code>composer.lock</code> file may have released newer versions since
the file was created). This is by design, ensuring that your project does not break because of
unexpected changes in dependencies.</p>
<p>So after fetching new changes from your VCS repository it is recommended to run
a Composer <code>install</code> to make sure the vendor directory is up in sync with your
<code>composer.lock</code> file.</p>
<pre><code class="language-bashell">php composer.phar install</code></pre>
<p>Composer enables reproducible builds by default. This means that running the
same command multiple times will produce a <code>vendor/</code> directory containing files
that are identical (<em>except their timestamps</em>), including the autoloader files.
It is especially beneficial for environments that require strict
verification processes, as well as for Linux distributions aiming to package PHP
applications in a secure and predictable manner.</p>
<h2 id="updating-dependencies-to-their-latest-versions">Updating dependencies to their latest versions<a href="#updating-dependencies-to-their-latest-versions" class="anchor">#</a></h2>
<p>As mentioned above, the <code>composer.lock</code> file prevents you from automatically getting
the latest versions of your dependencies. To update to the latest versions, use the
<a href="03-cli.md#update-u"><code>update</code></a> command. This will fetch the latest matching
versions (according to your <code>composer.json</code> file) and update the lock file
with the new versions.</p>
<pre><code class="language-bashell">php composer.phar update</code></pre>
<blockquote>
<p><strong>Note:</strong> Composer will display a Warning when executing an <code>install</code> command
if the <code>composer.lock</code> has not been updated since changes were made to the
<code>composer.json</code> that might affect dependency resolution.</p>
</blockquote>
<p>If you only want to install, upgrade or remove one dependency, you can explicitly list it as an argument:</p>
<pre><code class="language-bashell">php composer.phar update monolog/monolog [...]</code></pre>
<h2 id="packagist">Packagist<a href="#packagist" class="anchor">#</a></h2>
<p><a href="https://packagist.org/">Packagist.org</a> is the main Composer repository. A Composer
repository is basically a package source: a place where you can get packages
from. Packagist aims to be the central repository that everybody uses. This
means that you can automatically <code>require</code> any package that is available there,
without further specifying where Composer should look for the package.</p>
<p>If you go to the <a href="https://packagist.org/">Packagist.org website</a>,
you can browse and search for packages.</p>
<p>Any open source project using Composer is recommended to publish their packages
on Packagist. A library does not need to be on Packagist to be used by Composer,
but it enables discovery and adoption by other developers more quickly.</p>
<h2 id="platform-packages">Platform packages<a href="#platform-packages" class="anchor">#</a></h2>
<p>Composer has platform packages, which are virtual packages for things that are
installed on the system but are not actually installable by Composer. This
includes PHP itself, PHP extensions and some system libraries.</p>
<ul>
<li>
<p><code>php</code> represents the PHP version of the user, allowing you to apply
constraints, e.g. <code>^7.1</code>. To require a 64bit version of php, you can
require the <code>php-64bit</code> package.</p>
</li>
<li>
<p><code>hhvm</code> represents the version of the HHVM runtime and allows you to apply
a constraint, e.g., <code>^2.3</code>.</p>
</li>
<li>
<p><code>ext-&lt;name&gt;</code> allows you to require PHP extensions (includes core
extensions). Versioning can be quite inconsistent here, so it's often
a good idea to set the constraint to <code>*</code>.  An example of an extension
package name is <code>ext-gd</code>.</p>
</li>
<li>
<p><code>lib-&lt;name&gt;</code> allows constraints to be made on versions of libraries used by
PHP. The following are available: <code>curl</code>, <code>iconv</code>, <code>icu</code>, <code>libxml</code>,
<code>openssl</code>, <code>pcre</code>, <code>uuid</code>, <code>xsl</code>.</p>
</li>
</ul>
<p>You can use <a href="03-cli.md#show"><code>show --platform</code></a> to get a list of your locally
available platform packages.</p>
<h2 id="autoloading">Autoloading<a href="#autoloading" class="anchor">#</a></h2>
<p>For libraries that specify autoload information, Composer generates a
<code>vendor/autoload.php</code> file. You can include this file and start
using the classes that those libraries provide without any extra work:</p>
<pre><code class="language-php">require __DIR__ . '/vendor/autoload.php';

$log = new Monolog\Logger('name');
$log-&gt;pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::WARNING));
$log-&gt;warning('Foo');</code></pre>
<p>You can even add your own code to the autoloader by adding an
<a href="04-schema.md#autoload"><code>autoload</code></a> field to <code>composer.json</code>.</p>
<pre><code class="language-javascript">{
    "autoload": {
        "psr-4": {"Acme\\": "src/"}
    }
}</code></pre>
<p>Composer will register a <a href="https://www.php-fig.org/psr/psr-4/">PSR-4</a> autoloader
for the <code>Acme</code> namespace.</p>
<p>You define a mapping from namespaces to directories. The <code>src</code> directory would
be in your project root, on the same level as the <code>vendor</code> directory. An example
filename would be <code>src/Foo.php</code> containing an <code>Acme\Foo</code> class.</p>
<p>After adding the <a href="04-schema.md#autoload"><code>autoload</code></a> field, you have to re-run
this command:</p>
<pre><code class="language-bashell">php composer.phar dump-autoload</code></pre>
<p>This command will re-generate the <code>vendor/autoload.php</code> file.
See the <a href="03-cli.md#dump-autoload-dumpautoload"><code>dump-autoload</code></a> section for
more information.</p>
<p>Including that file will also return the autoloader instance, so you can store
the return value of the include call in a variable and add more namespaces.
This can be useful for autoloading classes in a test suite, for example.</p>
<pre><code class="language-php">$loader = require __DIR__ . '/vendor/autoload.php';
$loader-&gt;addPsr4('Acme\\Test\\', __DIR__);</code></pre>
<p>In addition to PSR-4 autoloading, Composer also supports PSR-0, classmap and
files autoloading. See the <a href="04-schema.md#autoload"><code>autoload</code></a> reference for
more information.</p>
<p>See also the docs on <a href="articles/autoloader-optimization.md">optimizing the autoloader</a>.</p>
<blockquote>
<p><strong>Note:</strong> Composer provides its own autoloader. If you don't want to use that
one, you can include <code>vendor/composer/autoload_*.php</code> files, which return
associative arrays allowing you to configure your own autoloader.</p>
</blockquote>
<p class="prev-next">&larr; <a href="00-intro.md">Intro</a>  |  <a href="02-libraries.md">Libraries</a> &rarr;</p>

    <p class="fork-and-edit">
        Found a typo? Something is wrong in this documentation?
        <a href="https://github.com/composer/composer/edit/main/doc/01-basic-usage.md"
           title="Go to the docs to fork and propose updates (external link)"
           aria-label="Go to the docs to fork and propose updates (external link)">Fork and edit</a> it!
    </p>
                </div>
            </main>
            <footer>
                                
                <p class="license">
                    Composer and all content on this site are released under the <a href="https://github.com/composer/composer/blob/main/LICENSE" title="View the MIT license (external link to GitHub.com)" aria-label="View the MIT license (external link to GitHub.com)">MIT license</a>.
                </p>
            </footer>
        </div>

        <script src="/build/app.js?v=3"></script>
    </body>
</html>
