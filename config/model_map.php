<?php   
return array (
  'BaiduId' => 
  array (
    'model' => '\\App\\Models\\BaiduId',
    'table' => 'baidu_ids',
    'table_full_name' => 'ly_baidu_ids',
    'column' => 
    array (
      'id' => 'bigint',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'FundFund' => 
  array (
    'model' => '\\App\\Models\\Fund\\Fund',
    'table' => 'funds',
    'table_full_name' => 'ly_funds',
    'column' => 
    array (
      'id' => 'bigint',
      'code' => 'string',
      'name' => 'string',
      'pinyin_initial' => 'string',
      'type' => 'string',
      'unit_net_value' => 'decimal',
      'cumulative_net_value' => 'decimal',
      'net_value_time' => 'integer',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'FundFundNetValue' => 
  array (
    'model' => '\\App\\Models\\Fund\\FundNetValue',
    'table' => 'fund_net_values',
    'table_full_name' => 'ly_fund_net_values',
    'column' => 
    array (
      'id' => 'bigint',
      'fund_id' => 'bigint',
      'code' => 'string',
      'name' => 'string',
      'unit_net_value' => 'decimal',
      'cumulative_net_value' => 'decimal',
      'net_value_time' => 'integer',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'FundFundValuation' => 
  array (
    'model' => '\\App\\Models\\Fund\\FundValuation',
    'table' => 'fund_valuations',
    'table_full_name' => 'ly_fund_valuations',
    'column' => 
    array (
      'id' => 'bigint',
      'fund_id' => 'bigint',
      'code' => 'string',
      'name' => 'string',
      'unit_net_value' => 'decimal',
      'estimated_net_value' => 'decimal',
      'estimated_growth' => 'decimal',
      'estimated_growth_rate' => 'decimal',
      'valuation_time' => 'integer',
      'valuation_source' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'IntelIntelProduct' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProduct',
    'table' => 'intel_products',
    'table_full_name' => 'ly_intel_products',
    'column' => 
    array (
      'id' => 'bigint',
      'language' => 'string',
      'unique_key' => 'string',
      'category_id' => 'bigint',
      'series_id' => 'bigint',
      'ark_series_id' => 'string',
      'ark_product_id' => 'string',
      'name' => 'string',
      'path' => 'string',
      'url' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'IntelIntelProductCategory' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProductCategory',
    'table' => 'intel_product_categories',
    'table_full_name' => 'ly_intel_product_categories',
    'column' => 
    array (
      'id' => 'bigint',
      'language' => 'string',
      'unique_key' => 'string',
      'pid' => 'bigint',
      'level' => 'bigint',
      'panel_key' => 'string',
      'name' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'IntelIntelProductSeries' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProductSeries',
    'table' => 'intel_product_series',
    'table_full_name' => 'ly_intel_product_series',
    'column' => 
    array (
      'id' => 'bigint',
      'language' => 'string',
      'unique_key' => 'string',
      'category_id' => 'bigint',
      'ark_series_id' => 'string',
      'name' => 'string',
      'path' => 'string',
      'url' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'IntelIntelProductSpec' => 
  array (
    'model' => '\\App\\Models\\Intel\\IntelProductSpec',
    'table' => 'intel_product_specs',
    'table_full_name' => 'ly_intel_product_specs',
    'column' => 
    array (
      'id' => 'bigint',
      'language' => 'string',
      'unique_key' => 'string',
      'category_id' => 'bigint',
      'series_id' => 'bigint',
      'product_id' => 'bigint',
      'ark_series_id' => 'string',
      'ark_product_id' => 'string',
      'tab_index' => 'boolean',
      'tab_title' => 'string',
      'key' => 'string',
      'label' => 'string',
      'value' => 'text',
      'value_url' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
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
  'SystemSystemFile' => 
  array (
    'model' => '\\App\\Models\\System\\SystemFile',
    'table' => 'system_files',
    'table_full_name' => 'ly_system_files',
    'column' => 
    array (
      'id' => 'bigint',
      'name' => 'string',
      'path' => 'string',
      'origin_name' => 'string',
      'mime_type' => 'string',
      'extension' => 'string',
      'size' => 'string',
      'width' => 'string',
      'height' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'SystemSystemLanguage' => 
  array (
    'model' => '\\App\\Models\\System\\SystemLanguage',
    'table' => 'system_languages',
    'table_full_name' => 'ly_system_languages',
    'column' => 
    array (
      'id' => 'bigint',
      'key' => 'string',
      'name' => 'string',
      'locale' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'SystemSystemOption' => 
  array (
    'model' => '\\App\\Models\\System\\SystemOption',
    'table' => 'system_options',
    'table_full_name' => 'ly_system_options',
    'column' => 
    array (
      'id' => 'bigint',
      'name' => 'string',
      'code' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'SystemSystemOptionItem' => 
  array (
    'model' => '\\App\\Models\\System\\SystemOptionItem',
    'table' => 'system_option_items',
    'table_full_name' => 'ly_system_option_items',
    'column' => 
    array (
      'id' => 'bigint',
      'code' => 'string',
      'value' => 'string',
      'sort' => 'boolean',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'User' => 
  array (
    'model' => '\\App\\Models\\User',
    'table' => 'users',
    'table_full_name' => 'ly_users',
    'column' => 
    array (
      'id' => 'bigint',
      'user_name' => 'string',
      'nick_name' => 'string',
      'real_name' => 'string',
      'mobile' => 'string',
      'email' => 'string',
      'email_verified_at' => 'integer',
      'previous_avatar' => 'string',
      'avatar' => 'string',
      'sex' => 'boolean',
      'birthdate' => 'integer',
      'last_login_time' => 'integer',
      'status' => 'boolean',
      'frozen_time' => 'integer',
      'open_id' => 'string',
      'password' => 'string',
      'o_pwd' => 'text',
      'remember_token' => 'string',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
  'UserFund' => 
  array (
    'model' => '\\App\\Models\\UserFund',
    'table' => 'user_funds',
    'table_full_name' => 'ly_user_funds',
    'column' => 
    array (
      'id' => 'bigint',
      'user_id' => 'bigint',
      'fund_id' => 'bigint',
      'code' => 'string',
      'name' => 'string',
      'cost' => 'decimal',
      'share' => 'decimal',
      'amount' => 'decimal',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
);