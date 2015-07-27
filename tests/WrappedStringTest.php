<?php
/**
 * Copyright (c) 2015 Timo Tijhof <krinklemail@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @file
 */

namespace WrappedString\Test;

use WrappedString\WrappedString;

/**
 * @covers WrappedString\WrappedString
 */
class WrappedStringTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor() {
		$wstr = new WrappedString( '[', 'foo', ']' );
		$this->assertEquals(
			'[foo]',
			strval( $wstr ),
			'Construct and stringify'
		);
	}

	public static function provideCompact() {
		return array(
			array(
				'Merge consecutive strings that have the same before/after values',
				array(
					new WrappedString( '<foo>var q = q || [];', 'q.push( 0 );', '</foo>' ),
					new WrappedString( '<foo>var q = q || [];', 'q.push( 1 );', '</foo>' ),
					new WrappedString( '<foo>var q = q || [];', 'q.push( 2 );', '</foo>' ),
				),
				'<foo>var q = q || [];q.push( 0 );q.push( 1 );q.push( 2 );</foo>',
			),
			array(
				'Consecutive strings that look similar but have different dividers are not merged',
				array(
					new WrappedString( '<foo>var q = q || [];', 'q.push( 0 );', '</foo>' ),
					new WrappedString( '<foo>var q = q || [];', 'q.push( 1 );', '</foo>' ),
					new WrappedString( '<foo>', 'var q = q || [];q.push( 2 );', '</foo>' ),
					new WrappedString( '<foo>var q = q || [];', 'q.push( 3 );', '</foo>' ),
				),
				'<foo>var q = q || [];q.push( 0 );q.push( 1 );</foo>' . "\n" .
				'<foo>var q = q || [];q.push( 2 );</foo>' . "\n" .
				'<foo>var q = q || [];q.push( 3 );</foo>',
			),
			array(
				'Merge consecutive string that have an empty string prefix',
				array(
					new WrappedString( '<foo>var q = q || [];', 'q.push( 0 );', '</foo>' ),
					new WrappedString( '', '<foo special=a></foo>' ),
					new WrappedString( '', '<foo special=b></foo>' ),
					new WrappedString( '<foo special=c></foo>' ),
					new WrappedString( '<foo>var q = q || [];', 'q.push( 1 );', '</foo>' ),
				),
				'<foo>var q = q || [];q.push( 0 );</foo>' . "\n" .
				'<foo special=a></foo><foo special=b></foo>' . "\n" .
				'<foo special=c></foo>' . "\n" .
				'<foo>var q = q || [];q.push( 1 );</foo>',
			),
			array(
				'No merges when there are no consecutive strings with matching segments',
				array(
					new WrappedString( '<foo>var q = q || [];', 'q.push( 0 );', '</foo>' ),
					new WrappedString( '', '<foo special=a></foo>' ),
					new WrappedString( '<foo>var q = q || [];', 'q.push( 1 );', '</foo>' ),
					new WrappedString( '', '<foo special=b></foo>' ),
					new WrappedString( '<foo>var q = q || [];', 'q.push( 2 );', '</foo>' ),
				),
				'<foo>var q = q || [];q.push( 0 );</foo>' . "\n" .
				'<foo special=a></foo>' . "\n" .
				'<foo>var q = q || [];q.push( 1 );</foo>' . "\n" .
				'<foo special=b></foo>' . "\n" .
				'<foo>var q = q || [];q.push( 2 );</foo>',
			),
		);
	}

	/**
	 * @dataProvider provideCompact
	 */
	public function testCompact( $msg, $wraps, $expected ) {
		$this->assertEquals(
			$expected,
			WrappedString::join( "\n", $wraps ),
			$msg
		);
	}
}
