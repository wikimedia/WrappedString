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

namespace WrappedString\Test;

use WrappedString\WrappedString;
use WrappedString\WrappedStringList;

/**
 * @covers WrappedString\WrappedStringList
 */
class WrappedStringListTest extends \PHPUnit_Framework_TestCase {

	protected static function getFoo() {
		return new WrappedString( '[x]', '[', ']' );
	}

	protected static function getBar() {
		return new WrappedString( '{y}', '{', '}' );
	}

	public function testJoin() {
		$wraps1 = array(
			self::getFoo(),
			self::getFoo(),
			self::getFoo(),
			self::getBar(),
		);
		$list1 = WrappedString::join( "\n", $wraps1 );
		$wraps2 = array(
			self::getBar(),
			self::getFoo(),
			self::getBar(),
		);
		$list2 = WrappedString::join( "\n", $wraps2 );
		$this->assertEquals(
			"[xxx]\n{yy}\n[x]\n{y}",
			WrappedStringList::join( "\n", [ $list1, $list2 ] ),
			"Two compatible lists"
		);

		$wraps1 = array(
			self::getFoo(),
			self::getFoo(),
			self::getFoo(),
			self::getBar(),
		);
		$list1 = WrappedString::join( '', $wraps1 );
		$wraps2 = array(
			self::getBar(),
			self::getFoo(),
			self::getBar(),
		);
		$list2 = WrappedString::join( "\n", $wraps2 );
		$this->assertEquals(
			"[xxx]{y}\n{y}\n[x]\n{y}",
			WrappedStringList::join( "\n", [ $list1, $list2 ] ),
			"Two incompatible lists (different separator)"
		);

		$wraps1 = array(
			self::getFoo(),
			self::getFoo(),
			self::getFoo(),
			self::getBar(),
		);
		$list1 = WrappedString::join( '', $wraps1 );
		$wraps2 = array(
			self::getBar(),
		);
		$list2 = WrappedString::join( '', $wraps2 );
		$this->assertEquals(
			"meh[xxx]{yy}meh",
			WrappedStringList::join( '', [ 'meh', $list1, $list2, 'meh' ] ),
			"Two lists and a regular string"
		);
	}
}
