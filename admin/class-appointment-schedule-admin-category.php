<?php

class Category {

  const CATEGORY_TABLE = 'as_category';

  public static function create($categoryName) 
  {
    global $wpdb;
    if ($categoryName != '') {
      $table_name = $wpdb->prefix . self::CATEGORY_TABLE;
			$wpdb->insert($table_name, ['name' => $categoryName, 'created' => current_time('mysql')]);
    }
  }

  public static function get_all() 
  {
    global $wpdb;

    $table_name = $wpdb->prefix . self::CATEGORY_TABLE;
    $prepare = "SELECT id, name FROM $table_name";
    return $wpdb->get_results($prepare);
  }

  public static function get($categoryId) 
  {
    global $wpdb;
    try {
      $response['success'] = 1;
      $table_name = $wpdb->prefix . self::CATEGORY_TABLE;
      $prepare = "SELECT id, name FROM $table_name WHERE id = $categoryId";
      $response['category'] = $wpdb->get_row($prepare);
    } catch (\Exception $e) {
      $response['error'] = -1;
    }

    return $response;
  }

  public static function delete($categoryId) 
  {
    global $wpdb;
    try {
      $table_name = $wpdb->prefix . self::CATEGORY_TABLE;
      $wpdb->delete($table_name, [ 'ID' => $categoryId ]);
      self::unlinkFromService($categoryId);
      $response['success'] = 1;
    } catch (\Exception $e) {
      $response['error'] = -1;
      $response['message'] = $e->getMessage();
    }
    return $response;
  }

  private static function unlinkFromService($categoryId) 
  {
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'as_service', [ 'fk_category_id' => NULL ], [ 'fk_category_id' => $categoryId] );
  }

}