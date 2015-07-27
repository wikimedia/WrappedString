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

namespace WrappedString;

class WrappedString {
	/** @var string */
	protected $before;

	/** @var string */
	protected $content;

	/** @var string */
	protected $after;

	/**
	 * @param string $before
	 * @param string $content
	 * @param string $after
	 */
	public function __construct( $before, $content = '', $after = '' ) {
		$this->before = $before;
		$this->content = $content;
		$this->after = $after;
	}

	/**
	 * @param string $content
	 * @return WrappedString Newly wrapped string
	 */
	protected function extend( $content ) {
		$wrap = clone $this;
		$wrap->content .= $content;
		return $wrap;
	}

	/**
	 * Merge consecutive wrapped strings with the same before/after values.
	 *
	 * Does not modify the array or the WrappedString objects.
	 *
	 * @param WrappedString[] $wraps
	 * @return WrappedString[]
	 */
	protected static function compact( array &$wraps ) {
		$consolidated = array();
		$prev = current( $wraps );
		while ( ( $wrap = next( $wraps ) ) !== false ) {
			if ( $prev->before === $wrap->before && $prev->after === $wrap->after ) {
				$prev = $prev->extend( $wrap->content );
			} else {
				$consolidated[] = $prev;
				$prev = $wrap;
			}
		}
		// Add last one
		$consolidated[] = $prev;

		return $consolidated;
	}

	/**
	 * Join a several wrapped strings with a separator between each.
	 *
	 * @param string $sep
	 * @param WrappedString[] $wraps
	 * @return string
	 */
	public static function join( $sep, array $wraps ) {
		return implode( $sep, self::compact( $wraps ) );
	}

	/** @return string */
	public function __toString() {
		return $this->before . $this->content . $this->after;
	}
}
