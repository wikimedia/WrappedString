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
		$wstr = new WrappedString( '[foo]', '[', ']' );
		$this->assertEquals(
			'[foo]',
			strval( $wstr ),
			'Construct and stringify'
		);
	}

	protected static function getSquareBracketWrappedX() {
		return new WrappedString( '[x]', '[', ']' );
	}

	protected static function getCurlyBracketWrappedY() {
		return new WrappedString( '{y}', '{', '}' );
	}

	public function testJoin() {
		$wraps = [
			self::getSquareBracketWrappedX(),
			self::getSquareBracketWrappedX(),
			self::getSquareBracketWrappedX(),
			self::getCurlyBracketWrappedY(),
			self::getCurlyBracketWrappedY(),
			self::getSquareBracketWrappedX(),
			self::getCurlyBracketWrappedY(),
		];
		$this->assertEquals(
			'[xxx]{yy}[x]{y}',
			WrappedString::join( '', $wraps )
		);
	}

	public static function provideCompact() {
		return [
			[
				'Basic example',
				[
					new WrappedString( '[foo]', '[', ']' ),
					new WrappedString( '[bar]', '[', ']' ),
				],
				'[foobar]',
			],
			[
				'Mixed with primitive strings',
				[
					new WrappedString( '[foo]', '[', ']' ),
					new WrappedString( '[bar]', '[', ']' ),
					'[quux]',
					new WrappedString( '[baz]', '[', ']' ),
				],
				"[foobar]\n[quux]\n[baz]",
			],
			[
				'Merge consecutive strings that have the same before/after values',
				[
					new WrappedString( '<x>var q = q || [];q.push( 0 );</x>', '<x>var q = q || [];', '</x>' ),
					new WrappedString( '<x>var q = q || [];q.push( 1 );</x>', '<x>var q = q || [];', '</x>' ),
					new WrappedString( '<x>var q = q || [];q.push( 2 );</x>', '<x>var q = q || [];', '</x>' ),
				],
				'<x>var q = q || [];q.push( 0 );q.push( 1 );q.push( 2 );</x>',
			],
			[
				'Consecutive strings that look similar but have different dividers are not merged',
				[
					new WrappedString( '<x>var q = q || [];q.push( 0 );</x>', '<x>var q = q || [];', '</x>' ),
					new WrappedString( '<x>var q = q || [];q.push( 1 );</x>', '<x>var q = q || [];', '</x>' ),
					new WrappedString( '<x>var q = q || [];q.push( 2 );</x>', '<x>', '</x>' ),
					new WrappedString( '<x>var q = q || [];q.push( 3 );</x>', '<x>var q = q || [];', '</x>' ),
				],
				'<x>var q = q || [];q.push( 0 );q.push( 1 );</x>' . "\n" .
				'<x>var q = q || [];q.push( 2 );</x>' . "\n" .
				'<x>var q = q || [];q.push( 3 );</x>',
			],
			[
				'Merge consecutive string that have an empty string prefix',
				[
					new WrappedString( '<x>var q = q || [];q.push( 0 );</x>', '<x>var q = q || [];', '</x>' ),
					new WrappedString( '<x special=a></x>', '', '' ),
					new WrappedString( '<x special=b></x>', '', '' ),
					new WrappedString( '<x special=c></x>' ),
					new WrappedString( '<x>var q = q || [];q.push( 1 );</x>', '<x>var q = q || [];', '</x>' ),
				],
				'<x>var q = q || [];q.push( 0 );</x>' . "\n" .
				'<x special=a></x><x special=b></x>' . "\n" .
				'<x special=c></x>' . "\n" .
				'<x>var q = q || [];q.push( 1 );</x>',
			],
			[
				'No merges when there are no consecutive strings with matching segments',
				[
					new WrappedString( '<x>var q = q || [];q.push( 0 );</x>', '<x>var q = q || [];', '</x>' ),
					new WrappedString( '<x special=a></x>', '' ),
					new WrappedString( '<x>var q = q || [];q.push( 1 );</x>', '<x>var q = q || [];', '</x>' ),
					new WrappedString( '<x special=b></x>', '' ),
					new WrappedString( '<x>var q = q || [];q.push( 2 );</x>', '<x>var q = q || [];', '</x>' ),
				],
				'<x>var q = q || [];q.push( 0 );</x>' . "\n" .
				'<x special=a></x>' . "\n" .
				'<x>var q = q || [];q.push( 1 );</x>' . "\n" .
				'<x special=b></x>' . "\n" .
				'<x>var q = q || [];q.push( 2 );</x>',
			],
		];
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
