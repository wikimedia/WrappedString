<?php
namespace Wikimedia\Test;

use PHPUnit\Framework\TestCase;
use Wikimedia\WrappedString;
use Wikimedia\WrappedStringList;

/**
 * @covers \Wikimedia\WrappedStringList
 */
class WrappedStringListTest extends TestCase {

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
			"Empty list after empty string T196496" => [
				"\n",
				[
					new WrappedStringList( "\n", [
						"",
						new WrappedStringList( "\n", [
							new WrappedStringList( "\n", [] ),
						] ),
						self::getCurlyBracketWrappedY()
					] )
				],
				"\n{y}",
			],
			"Empty list in the middle T196496" => [
				"\n",
				[
					new WrappedStringList( "\n", [
						"A",
						new WrappedStringList( "\n", [
							new WrappedStringList( "\n", [] ),
						] ),
						self::getCurlyBracketWrappedY()
					] )
				],
				"A\n{y}",
			],
			// Booleans and null are not officially supported, but any non-WrappedString
			// values such other foreign objects with a toString() method, are
			// casted to a string using PHP's casting rules, which turns false
			// into an empty string.
			"List containing false" => [
				"\n",
				[
					new WrappedStringList( "\n", [
						"A",
						false,
						new WrappedStringList( "\n", [
							new WrappedStringList( "\n", [] ),
						] ),
						self::getCurlyBracketWrappedY()
					] )
				],
				"A\n\n{y}",
			],
			"List containing null" => [
				"\n",
				[
					new WrappedStringList( "\n", [
						"A",
						null,
						new WrappedStringList( "\n", [
							new WrappedStringList( "\n", [] ),
						] ),
						self::getCurlyBracketWrappedY()
					] )
				],
				"A\n\n{y}",
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
