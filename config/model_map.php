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
      'id' => 'integer',
      'code' => 'string',
      'name' => 'string',
      'pinyin_initial' => 'string',
      'type' => 'string',
      'unit_net_value' => 'float',
      'cumulative_net_value' => 'float',
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
      'id' => 'integer',
      'fund_id' => 'integer',
      'code' => 'string',
      'name' => 'string',
      'unit_net_value' => 'float',
      'cumulative_net_value' => 'float',
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
      'id' => 'integer',
      'fund_id' => 'integer',
      'code' => 'string',
      'name' => 'string',
      'unit_net_value' => 'float',
      'estimated_net_value' => 'float',
      'estimated_growth' => 'float',
      'estimated_growth_rate' => 'float',
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
      'id' => 'integer',
      'language' => 'string',
      'unique_key' => 'string',
      'category_id' => 'integer',
      'series_id' => 'integer',
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
      'id' => 'integer',
      'language' => 'string',
      'unique_key' => 'string',
      'pid' => 'integer',
      'level' => 'integer',
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
      'id' => 'integer',
      'language' => 'string',
      'unique_key' => 'string',
      'category_id' => 'integer',
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
      'id' => 'integer',
      'language' => 'string',
      'unique_key' => 'string',
      'category_id' => 'integer',
      'series_id' => 'integer',
      'product_id' => 'integer',
      'ark_series_id' => 'string',
      'ark_product_id' => 'string',
      'tab_index' => 'boolean',
      'tab_title' => 'string',
      'key' => 'string',
      'label' => 'string',
      'value' => 'string',
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
  'SystemAdministrativeDivision' => 
  array (
    'model' => '\\App\\Models\\System\\AdministrativeDivision',
    'table' => 'administrative_divisions',
    'table_full_name' => 'ly_administrative_divisions',
    'column' => 
    array (
      'id' => 'integer',
      'name' => 'string',
      'attr' => 'string',
      'code' => 'integer',
      'initial' => 'string',
      'pid' => 'integer',
      'level' => 'boolean',
      'sort' => 'boolean',
      'lat' => 'float',
      'lng' => 'float',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
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
      'id' => 'integer',
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
      'id' => 'integer',
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
      'id' => 'integer',
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
      'id' => 'integer',
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
      'o_pwd' => 'string',
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
      'id' => 'integer',
      'user_id' => 'integer',
      'fund_id' => 'integer',
      'code' => 'string',
      'name' => 'string',
      'cost' => 'float',
      'share' => 'float',
      'amount' => 'float',
      'created_at' => 'integer',
      'updated_at' => 'integer',
      'deleted_at' => 'integer',
    ),
  ),
);