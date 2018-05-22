<?php
/**
 * Copyright (c) 2016 Timo Tijhof <krinklemail@gmail.com>
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

namespace Wikimedia\Test;

use Wikimedia\WrappedString;
use Wikimedia\WrappedStringList;

/**
 * @covers Wikimedia\WrappedStringList
 */
class WrappedStringListTest extends \PHPUnit\Framework\TestCase {

	protected static function getSquareBracketWrappedX() {
		return new WrappedString( '[x]', '[', ']' );
	}

	protected static function getCurlyBracketWrappedY() {
		return new WrappedString( '{y}', '{', '}' );
	}

	public static function provideJoinCases() {
		return [
			'Two compatible lists' => [
				"\n",
				[
					WrappedString::join( "\n", [
						self::getSquareBracketWrappedX(),
						self::getSquareBracketWrappedX(),
						self::getSquareBracketWrappedX(),
						self::getCurlyBracketWrappedY(),
					] ),
					WrappedString::join( "\n", [
						self::getCurlyBracketWrappedY(),
						self::getSquareBracketWrappedX(),
						self::getCurlyBracketWrappedY(),
					] )
				],
				"[xxx]\n{yy}\n[x]\n{y}",
			],
			"Two incompatible lists (different separator)" => [
				"\n",
				[
					WrappedString::join( '', [
						self::getSquareBracketWrappedX(),
						self::getSquareBracketWrappedX(),
						self::getSquareBracketWrappedX(),
						self::getCurlyBracketWrappedY(),
					] ),
					WrappedString::join( "\n", [
						self::getCurlyBracketWrappedY(),
						self::getSquareBracketWrappedX(),
						self::getCurlyBracketWrappedY(),
					] ),
				],
				"[xxx]{y}\n{y}\n[x]\n{y}",
			],
			"Two lists and a regular string" => [
				'',
				[
					'meh',
					WrappedString::join( '', [
						self::getSquareBracketWrappedX(),
						self::getSquareBracketWrappedX(),
						self::getSquareBracketWrappedX(),
						self::getCurlyBracketWrappedY(),
					] ),
					WrappedString::join( '', [
						self::getCurlyBracketWrappedY(),
					] ),
					'meh',
				],
				"meh[xxx]{yy}meh"
			],
			"Lists, wraps, and regular strings" => [
				'',
				[
					self::getSquareBracketWrappedX(),
					new WrappedStringList( '', [
						self::getSquareBracketWrappedX(),
						self::getSquareBracketWrappedX(),
						self::getCurlyBracketWrappedY(),
					] ),
					self::getCurlyBracketWrappedY(),
					'meh'
				],
				"[xxx]{yy}meh",
			],
			"Nested lists" => [
				'',
				[
					new WrappedStringList( '', [
						new WrappedStringList( '', [
							self::getSquareBracketWrappedX(),
						] ),
						self::getSquareBracketWrappedX(),
						self::getCurlyBracketWrappedY(),
					] )
				],
				"[xx]{y}",
			],
		];
	}

	/**
	 * @dataProvider provideJoinCases
	 */
	public function testJoin( $sep, $lists, $expect ) {
		$this->assertEquals(
			$expect,
			WrappedStringList::join( $sep, $lists )
		);
	}

	public function testToString() {
		$list = new WrappedStringList( '', [
			self::getSquareBracketWrappedX(),
			self::getSquareBracketWrappedX(),
			self::getCurlyBracketWrappedY(),
		] );
		$this->assertEquals(
			"[xx]{y}",
			strval( $list ),
			"Join via native __toString"
		);
	}
}
