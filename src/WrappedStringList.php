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

namespace WrappedString;

class WrappedStringList {
	/** @var string */
	protected $sep;

	/** @var WrappedString[] */
	protected $wraps;

	/**
	 * @param string $sep
	 * @param WrappedString[] $wraps
	 */
	public function __construct( $sep, array $wraps ) {
		$this->sep = $sep;
		$this->wraps = $wraps;
	}

	/**
	 * @params WrappedString[] $wraps
	 * @return WrappedStringList Combined list
	 */
	protected function extend( array $wraps ) {
		$list = clone $this;
		$list->wraps = array_merge( $list->wraps, $wraps );
		return $list;
	}

	/**
	 * Merge lists with the same separator.
	 *
	 * Does not modify the given array or any of the objects in it.
	 *
	 * @param WrappedStringList[] $wraps
	 * @return string[] Compacted list
	 */
	protected static function compact( array $lists ) {
		$consolidated = array();
		$prev = current( $lists );
		while ( ( $curr = next( $lists ) ) !== false ) {
			if ( $prev instanceof WrappedStringList ) {
				if ( $curr instanceof WrappedStringList
					&& $prev->sep === $curr->sep
				) {
					// Merge previous and current list
					$prev = $prev->extend( $curr->wraps );
				} else {
					// Current one not mergeable. Compact previous one.
					$prev = implode( $prev->sep, WrappedString::compact( $prev ) );
				}
			} else {
				$consolidated[] = $prev;
				$prev = $curr;
			}
		}

		// Add last one
		if ( $prev instanceof WrappedStringList ) {
			$consolidated[] = implode( $prev->sep, WrappedString::compact( $prev->wraps ) );
		} else {
			$consolidated[] = $prev;
		}

		return $consolidated;
	}

	/**
	 * Join a several wrapped strings with a separator between each.
	 *
	 * @param string $sep
	 * @param array $lists Array of strings and/or WrappedStringList objects
	 * @return string
	 */
	public static function join( $sep, array $lists ) {
		return implode( $sep, self::compact( $lists ) );
	}

	/** @return string */
	public function __toString() {
		return self::join( $this->sep, [ $this ] );
	}
}
