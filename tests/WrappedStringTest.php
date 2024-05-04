<?php
namespace Wikimedia\Test;

use DomainException;
use PHPUnit\Framework\TestCase;
use Wikimedia\WrappedString;

/**
 * @covers \Wikimedia\WrappedString
 */
class WrappedStringTest extends TestCase {

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

	public function testInvalidPrefix() {
		$this->expectException( DomainException::class );
		$this->expectExceptionMessage( 'Prefix must match' );
		new WrappedString( 'Hello</x>', '<x>', '</x>' );
	}

	public function testInvalidSuffix() {
		$this->expectException( DomainException::class );
		$this->expectExceptionMessage( 'Suffix must match' );
		new WrappedString( '<x>world', '<x>', '</x>' );
	}
}
