<?php

require_once( 'MockWordPress.php' );

use PHPUnit\Framework\TestCase;
use WaughJ\WPPostListConverter\WPPostListConverter;

class WPPostListConverterTest extends TestCase
{
	public function testIsArray()
	{
		$converter = new WPPostListConverter([ 'type' => 'menu' ]);
		$this->assertTrue( is_array( $converter->getConvertedList( $this->getWPList() ) ) );
	}

	public function testHasExpectedValues()
	{
		$converter = new WPPostListConverter([ 'type' => 'menu' ]);
		$list = $converter->getConvertedList( $this->getWPList() );
		$this->assertTrue( !empty( $list ) );
		$this->assertEquals( 1, count( $list ) );

		foreach( $list as $list_item )
		{
			$this->assertTrue( is_array( $list_item ) );
			$this->assertTrue( array_key_exists( 'id', $list_item ) );
			$this->assertTrue( array_key_exists( 'title', $list_item ) );
			$this->assertTrue( array_key_exists( 'url', $list_item ) );
			$this->assertTrue( array_key_exists( 'subnav', $list_item ) );
			$this->assertEquals( 1, $list_item[ 'id' ] );
			$this->assertEquals( "Some Post", $list_item[ 'title' ] );
			$this->assertEquals( 'https://www.jaimeson-waugh.com', $list_item[ 'url' ] );

			foreach( $list_item[ 'subnav' ] as $subitem )
			{
				$this->assertTrue( is_array( $subitem ) );
				$this->assertTrue( array_key_exists( 'id', $subitem ) );
				$this->assertTrue( array_key_exists( 'title', $subitem ) );
				$this->assertTrue( array_key_exists( 'url', $subitem ) );
				$this->assertEquals( 2, $subitem[ 'id' ] );
				$this->assertEquals( "Some Post Child", $subitem[ 'title' ] );
				$this->assertEquals( 'https://www.jaimeson-waugh.com', $subitem[ 'url' ] );
			}
		}
	}

	public function testNormalListConverter()
	{
		$converter = new WPPostListConverter([ 'type' => 'normal' ]);
		$list = $converter->getConvertedList( $this->getWPList() );
		$this->assertTrue( !empty( $list ) );
		$this->assertEquals( 1, count( $list ) );

		foreach( $list as $list_item )
		{
			$this->assertTrue( is_array( $list_item ) );
			$this->assertTrue( array_key_exists( 'id', $list_item ) );
			$this->assertTrue( array_key_exists( 'title', $list_item ) );
			$this->assertTrue( array_key_exists( 'url', $list_item ) );
			$this->assertTrue( array_key_exists( 'subnav', $list_item ) );
			$this->assertEquals( 1, $list_item[ 'id' ] );
			$this->assertEquals( "Some Post", $list_item[ 'title' ] );
			$this->assertEquals( 'https://www.jaimeson-waugh.com', $list_item[ 'url' ] );

			foreach( $list_item[ 'subnav' ] as $subitem )
			{
				$this->assertTrue( is_array( $subitem ) );
				$this->assertTrue( array_key_exists( 'id', $subitem ) );
				$this->assertTrue( array_key_exists( 'title', $subitem ) );
				$this->assertTrue( array_key_exists( 'url', $subitem ) );
				$this->assertEquals( 2, $subitem[ 'id' ] );
				$this->assertEquals( "Some Post Child", $subitem[ 'title' ] );
				$this->assertEquals( 'https://www.jaimeson-waugh.com', $subitem[ 'url' ] );
			}
		}
	}

	private function getWPList() : array
	{
		global $post_list;
		return $post_list;
	}
}
