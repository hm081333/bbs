<?php   
return array (
  'BaiduId' => 
  array (
    'model' => '\\App\\Models\\BaiduId',
    'table' => 'baidu_ids',
    'table_full_name' => 'ly_baidu_ids',
    'column' => 
    array (
      'id' => 'integer',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'FundFund' => 
  array (
    'model' => '\\App\\Models\\Fund\\Fund',
    'table' => 'funds',
    'table_full_name' => 'ly_funds',
    'column' => 
    array (
      'id' => 'integer',
      'code' => 'varchar',
      'name' => 'varchar',
      'pinyin_initial' => 'varchar',
      'type' => 'varchar',
      'unit_net_value' => 'float',
      'cumulative_net_value' => 'float',
      'net_value_time' => 'int',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'FundFundNetValue' => 
  array (
    'model' => '\\App\\Models\\Fund\\FundNetValue',
    'table' => 'fund_net_values',
    'table_full_name' => 'ly_fund_net_values',
    'column' => 
    array (
      'id' => 'integer',
      'fund_id' => 'integer',
      'code' => 'varchar',
      'name' => 'varchar',
      'unit_net_value' => 'float',
      'cumulative_net_value' => 'float',
      'net_value_time' => 'int',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'FundFundValuation' => 
  array (
    'model' => '\\App\\Models\\Fund\\FundValuation',
    'table' => 'fund_valuations',
    'table_full_name' => 'ly_fund_valuations',
    'column' => 
    array (
      'id' => 'integer',
      'fund_id' => 'integer',
      'code' => 'varchar',
      'name' => 'varchar',
      'unit_net_value' => 'float',
      'estimated_net_value' => 'float',
      'estimated_growth' => 'float',
      'estimated_growth_rate' => 'float',
      'valuation_time' => 'int',
      'valuation_source' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'IntelIntelProduct' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProduct',
    'table' => 'intel_products',
    'table_full_name' => 'ly_intel_products',
    'column' => 
    array (
      'id' => 'integer',
      'language' => 'enum',
      'unique_key' => 'varchar',
      'category_id' => 'integer',
      'series_id' => 'integer',
      'ark_series_id' => 'varchar',
      'ark_product_id' => 'varchar',
      'name' => 'varchar',
      'path' => 'varchar',
      'url' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'IntelIntelProductCategory' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProductCategory',
    'table' => 'intel_product_categories',
    'table_full_name' => 'ly_intel_product_categories',
    'column' => 
    array (
      'id' => 'integer',
      'language' => 'enum',
      'unique_key' => 'varchar',
      'pid' => 'integer',
      'level' => 'integer',
      'panel_key' => 'varchar',
      'name' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'IntelIntelProductSeries' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProductSeries',
    'table' => 'intel_product_series',
    'table_full_name' => 'ly_intel_product_series',
    'column' => 
    array (
      'id' => 'integer',
      'language' => 'enum',
      'unique_key' => 'varchar',
      'category_id' => 'integer',
      'ark_series_id' => 'varchar',
      'name' => 'varchar',
      'path' => 'varchar',
      'url' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'IntelIntelProductSpec' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProductSpec',
    'table' => 'intel_product_specs',
    'table_full_name' => 'ly_intel_product_specs',
    'column' => 
    array (
      'id' => 'integer',
      'language' => 'enum',
      'unique_key' => 'varchar',
      'category_id' => 'integer',
      'series_id' => 'integer',
      'product_id' => 'integer',
      'ark_series_id' => 'varchar',
      'ark_product_id' => 'varchar',
      'tab_index' => 'tinyint',
      'tab_title' => 'varchar',
      'key' => 'varchar',
      'label' => 'varchar',
      'value' => 'string',
      'value_url' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'MongodbAccessLog' => 
  array (
    'model' => '\\App\\Models\\Mongodb\\AccessLog',
    'table' => 'access_log',
    'table_full_name' => 'access_log',
    'column' => 
    array (
    ),
  ),
  'MongodbSqlLog' => 
  array (
    'model' => '\\App\\Models\\Mongodb\\SqlLog',
    'table' => 'sql_log',
    'table_full_name' => 'sql_log',
    'column' => 
    array (
    ),
  ),
  'SystemAdministrativeDivision' => 
  array (
    'model' => '\\App\\Models\\System\\AdministrativeDivision',
    'table' => 'administrative_divisions',
    'table_full_name' => 'ly_administrative_divisions',
    'column' => 
    array (
      'id' => 'integer',
      'name' => 'varchar',
      'attr' => 'varchar',
      'code' => 'integer',
      'initial' => 'varchar',
      'pid' => 'integer',
      'level' => 'tinyint',
      'sort' => 'tinyint',
      'lat' => 'float',
      'lng' => 'float',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'SystemSystemConfig' => 
  array (
    'model' => '\\App\\Models\\System\\SystemConfig',
    'table' => 'system_configs',
    'table_full_name' => 'ly_system_configs',
    'column' => 
    array (
      'id' => 'integer',
      'unique' => 'varchar',
      'type' => 'varchar',
      'key' => 'varchar',
      'value' => 'longtext',
      'data_type' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'SystemSystemFile' => 
  array (
    'model' => '\\App\\Models\\System\\SystemFile',
    'table' => 'system_files',
    'table_full_name' => 'ly_system_files',
    'column' => 
    array (
      'id' => 'integer',
      'name' => 'varchar',
      'path' => 'varchar',
      'origin_name' => 'varchar',
      'mime_type' => 'varchar',
      'extension' => 'varchar',
      'size' => 'varchar',
      'width' => 'varchar',
      'height' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'SystemSystemLanguage' => 
  array (
    'model' => '\\App\\Models\\System\\SystemLanguage',
    'table' => 'system_languages',
    'table_full_name' => 'ly_system_languages',
    'column' => 
    array (
      'id' => 'integer',
      'key' => 'varchar',
      'name' => 'varchar',
      'locale' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'SystemSystemOption' => 
  array (
    'model' => '\\App\\Models\\System\\SystemOption',
    'table' => 'system_options',
    'table_full_name' => 'ly_system_options',
    'column' => 
    array (
      'id' => 'integer',
      'name' => 'varchar',
      'code' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'SystemSystemOptionItem' => 
  array (
    'model' => '\\App\\Models\\System\\SystemOptionItem',
    'table' => 'system_option_items',
    'table_full_name' => 'ly_system_option_items',
    'column' => 
    array (
      'id' => 'integer',
      'code' => 'varchar',
      'value' => 'varchar',
      'sort' => 'tinyint',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'User' => 
  array (
    'model' => '\\App\\Models\\User',
    'table' => 'users',
    'table_full_name' => 'ly_users',
    'column' => 
    array (
      'id' => 'integer',
      'user_name' => 'varchar',
      'nick_name' => 'varchar',
      'real_name' => 'varchar',
      'mobile' => 'varchar',
      'email' => 'varchar',
      'email_verified_at' => 'int',
      'previous_avatar' => 'varchar',
      'avatar' => 'varchar',
      'sex' => 'tinyint',
      'birthdate' => 'int',
      'last_login_time' => 'int',
      'status' => 'tinyint',
      'frozen_time' => 'int',
      'open_id' => 'varchar',
      'password' => 'varchar',
      'o_pwd' => 'string',
      'remember_token' => 'varchar',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
  'UserFund' => 
  array (
    'model' => '\\App\\Models\\UserFund',
    'table' => 'user_funds',
    'table_full_name' => 'ly_user_funds',
    'column' => 
    array (
      'id' => 'integer',
      'user_id' => 'integer',
      'fund_id' => 'integer',
      'code' => 'varchar',
      'name' => 'varchar',
      'cost' => 'float',
      'share' => 'float',
      'amount' => 'float',
      'created_at' => 'int',
      'updated_at' => 'int',
      'deleted_at' => 'int',
    ),
  ),
);