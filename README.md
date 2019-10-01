WP Post List Converter
=========================

Convert WordPress’s list of posts into hierarchical list easier to work with.

Takes in an array o’ arguments: "type" tells converter whether list is o’ regular posts or items from an admin menu; "includes" is used for keeping extra info from WP_Post object.

## Use

    use WaughJ\WPPostListConverter\WPPostListConverter;
    $converter = new WPPostListConverter();
    $new_list = $converter->getConvertedList( $old_list );

## Changelog

### 0.2.1
* Fix bug causing 3rd-layer & onward subnavs to not work

### 0.2.0
* Add extra menu item data collected by default

### 0.1.3
* Update TestHashItem Dependency

### 0.1.2
* Make Compatible with WordPress Plugin Rules

### 0.1.1
* Add Readme

### 0.1.0
* Initial Release
