WP Post List Converter
=========================

Convert WordPress’s list of posts into hierarchical list easier to work with.

Takes in an array o’ arguments: "type" tells converter whether list is o’ regular posts or items from an admin menu; "includes" is used for keeping extra info from WP_Post object.

## Use

    use WaughJ\WPPostListConverter\WPPostListConverter;
    $converter = new WPPostListConverter();
    $new_list = $converter->getConvertedList( $old_list );

## Changelog

### 0.2.0
* Add extra menu item data collected by default.

### 0.1.0
* Initial Release
