WrappedString
=============

WrappedString is a small PHP library for compacting redundant string-wrapping
code in text output. The most common use-case is to eliminate redundant runs of
HTML open/close tags and JavaScript boilerplate.

Here is how you use it:

<pre lang="php">
use WrappedString\WrappedString;

$buffer = array(
	new WrappedString( '<script>var q = q || [];', 'q.push( 0 );', '</script>' ),
	new WrappedString( '<script>var q = q || [];', 'q.push( 1 );', '</script>' ),
);
$output = WrappedString::join( "\n", $buffer );
// Result: '<script>var q = q || [];q.push( 0 );q.push( 1 );</script>'
</pre>

License
-------

The project is licensed under the MIT license.
