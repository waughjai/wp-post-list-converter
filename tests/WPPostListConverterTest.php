<?php

require_once( 'MockWordPress.php' );

use PHPUnit\Framework\TestCase;
use WaughJ\WPPostListConverter\WPPostListConverter;

class WPPostListConverterTest extends TestCase
{
	public function testHasExpectedValues()
	{
		global $menu;
		$converter = new WPPostListConverter([ 'type' => 'menu' ]);
		$list = $converter->getConvertedList( $menu );
		$this->assertTrue( !empty( $list ) );
		$this->assertEquals( 1, count( $list ) );

		foreach( $list as $list_item )
		{
			$this->assertEquals( 1, $list_item[ 'id' ] );
			$this->assertEquals( 2, $list_item[ 'object_id' ] );
			$this->assertEquals( "Some Post", $list_item[ 'title' ] );
			$this->assertEquals( 'https://www.jaimeson-waugh.com', $list_item[ 'url' ] );
			$this->assertEquals( 'nav_menu_item', $list_item[ 'type' ] );
			$this->assertEquals( 'post', $list_item[ 'object_type' ] );

			foreach( $list_item[ 'subnav' ] as $subitem )
			{
				$this->assertEquals( 2, $subitem[ 'id' ] );
				$this->assertEquals( 4, $subitem[ 'object_id' ] );
				$this->assertEquals( "Some Post Child", $subitem[ 'title' ] );
				$this->assertEquals( 'https://www.jaimeson-waugh.com', $subitem[ 'url' ] );
				$this->assertEquals( 'nav_menu_item', $subitem[ 'type' ] );
				$this->assertEquals( 'post', $subitem[ 'object_type' ] );
			}
		}
	}

	public function testNormalListConverter()
	{
		global $post_list;
		$converter = new WPPostListConverter([ 'type' => 'normal' ]);
		$list = $converter->getConvertedList( $post_list );
		$this->assertTrue( !empty( $list ) );
		$this->assertEquals( 3, count( $list ) );

		foreach( $list as $list_item )
		{
			$this->assertEquals( $list_item[ 'id' ], $list_item[ 'object_id' ] );
			$this->assertEquals( 'post', $list_item[ 'type' ] );
			$this->assertEquals( 'post', $list_item[ 'object_type' ] );

			if ( array_key_exists( 'subnav', $list_item ) )
			{
				foreach( $list_item[ 'subnav' ] as $subitem )
				{
					$this->assertEquals( $subitem[ 'id' ], $subitem[ 'object_id' ] );
					$this->assertEquals( 'post', $subitem[ 'type' ] );
					$this->assertEquals( 'post', $subitem[ 'object_type' ] );
				}
			}
		}
	}
}
