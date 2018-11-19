<?php

declare( strict_types = 1 );
namespace WaughJ\WPPostListConverter
{
	use function WaughJ\TestHashItem\TestHashItemArray;
	use function WaughJ\TestHashItem\TestHashItemString;

	class WPPostListConverter
	{

	//
	//  PUBLIC
	//
	/////////////////////////////////////////////////////////

		public function __construct( array $args = [] )
		{
			$this->args = $args;
			$this->args[ 'type' ] = self::decideType( $args );
		}

		public function getConvertedList( array $wordpress_menu_data )
		{
			$new_menu = [];

			// Get parent menu items. Delete parent items from $wordpress_menu_data.
			$new_menu = $this->getMenuTopLevel( $wordpress_menu_data );

			// Realign wordpress menu data to clear out deleted indices.
			$wordpress_menu_data = array_values( $wordpress_menu_data );

			// Get children menu items. Delete children items from $wordpress_menu_data.
			$this->addChildrenToMenu( $new_menu, $wordpress_menu_data );

			return $new_menu;
		}




	//
	//  PRIVATE
	//
	/////////////////////////////////////////////////////////

		private function addChildrenToMenu( array &$new_menu, array &$wordpress_menu_data ) : void
		{
			$did_something = true;
			$wordpress_menu_data_count = count( $wordpress_menu_data );
			while ( $wordpress_menu_data_count > 0 && $did_something )
			{
				$did_something = false;
				for ( $i = 0; $i < $wordpress_menu_data_count; $i++ )
				{
					$wordpress_menu_item = $wordpress_menu_data[ $i ];
					if ( is_a( $wordpress_menu_item, '\WP_Post' ) )
					{
						if ( $this->testListForSubItem( $new_menu, $wordpress_menu_item ) )
						{
							unset( $wordpress_menu_data[ $i ] );
							$did_something = true;
						}
					}
				}

				// Realign wordpress menu data to clear out deleted indices.
				$wordpress_menu_data = array_values( $wordpress_menu_data );

				// Since the count is different, we need to change our count.
				$wordpress_menu_data_count = count( $wordpress_menu_data );
			}
		}

		private function testListForSubItem( array &$new_menu, \WP_Post $wordpress_menu_item ) : bool
		{
			// Look for parent of child menu item & give new item to them.
			foreach ( $new_menu as $new_menu_key => $new_menu_item )
			{
				// If parent is found...
				if ( $new_menu_key == $this->getItemParent( $wordpress_menu_item ) )
				{
					// Make sure parent has subnav array.
					if ( !isset( $new_menu[ $new_menu_key ][ 'subnav' ] ) )
					{
						$new_menu[ $new_menu_key ][ 'subnav' ] = [];
					}

					$new_menu[ $new_menu_key ][ 'subnav' ][ $wordpress_menu_item->ID ] = $this->createNewItemFromWordPressItem( $wordpress_menu_item );
					return true;
				}
				else
				{
					// If isn't parent, but has children of their own,
					// search through their children, recursively, too.
					if ( isset( $new_menu_item[ 'subnav' ] ) )
					{
						$found_in_child = $this->testListForSubItem( $new_menu_item[ 'subnav' ], $wordpress_menu_item );

						// If 'twas found in child, we're done, return with a success flag.
						// Otherwise, we continue.
						if ( $found_in_child )
						{
							return true;
						}
					}
				}
			}

			// We didn't find parent anywhere.
			return false;
		}

		private function getMenuTopLevel( array &$wordpress_menu_data ) : array
		{
			$new_menu = [];
			$wordpress_menu_data_count = count( $wordpress_menu_data );
			for ( $i = 0; $i < $wordpress_menu_data_count; $i++ )
			{
				$wordpress_menu_item = $wordpress_menu_data[ $i ];
				if ( $this->testMenuItemOnTopLevel( $wordpress_menu_item ) )
				{
					$new_menu[ $wordpress_menu_item->ID ] = $this->createNewItemFromWordPressItem( $wordpress_menu_item );
					unset( $wordpress_menu_data[ $i ] );
				}
			}
			return $new_menu;
		}

		private function createNewItemFromWordPressItem( \WP_Post $wordpress_item ) : array
		{
			return array_merge
			(
				[
					'id'    => $wordpress_item->ID,
					'title' => $this->getItemTitle( $wordpress_item ),
					'url'   => $this->getItemURL( $wordpress_item )
				],
				$this->getIncludes( $wordpress_item )
			);
		}

		private function getIncludes( \WP_Post $wordpress_item ) : array
		{
			$rows = [];
			$includes = TestHashItemArray( $this->args, 'includes', false );
			if ( $includes !== false )
			{
				foreach ( $includes as $include_key => $include_value )
				{
					if ( is_string( $include_value ) && isset( $wordpress_item->{ $include_value } ) )
					{
						$rows[ $include_key ] = $include_value;
					}
				}
			}
			return $rows;
		}

		private function testMenuItemOnTopLevel( \WP_Post $menu_item ) : bool
		{
			return $this->getItemParent( $menu_item ) == "0";
		}

		private function getItemURL( \WP_Post $menu_item ) : string
		{
			return $this->args[ 'type' ] === 'menu' ? $menu_item->url : get_permalink( $menu_item );
		}

		private function getItemTitle( \WP_Post $menu_item ) : string
		{
			return $menu_item->{ $this->getTitleTypeName() };
		}

		private function getTitleTypeName() : string
		{
			return $this->args[ 'type' ] === 'menu' ? 'title' : 'post_title';
		}

		private function getItemParent( \WP_Post $menu_item ) : int
		{
			return intval( $menu_item->{ $this->getParentTypeName() } );
		}

		private function getParentTypeName() : string
		{
			return $this->args[ 'type' ] === 'menu' ? 'menu_item_parent' : 'post_parent';
		}

		private static function decideType( array $args ) : string
		{
			$type = TestHashItemString( $args, 'type', self::VALID_TYPES[ 0 ] );
			return in_array( $type, self::VALID_TYPES ) ? $type : self::VALID_TYPES[ 0 ];
		}

		private $args;
		private const VALID_TYPES =
		[
			'normal',
			'menu'
		];
	}
}
