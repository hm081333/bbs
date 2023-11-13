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
  'File' => 
  array (
    'model' => '\\App\\Models\\File',
    'table' => 'files',
    'table_full_name' => 'ly_files',
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
  'Option' => 
  array (
    'model' => '\\App\\Models\\Option',
    'table' => 'options',
    'table_full_name' => 'ly_options',
    'column' => 
    array (
    ),
  ),
  'OptionItem' => 
  array (
    'model' => '\\App\\Models\\OptionItem',
    'table' => 'option_items',
    'table_full_name' => 'ly_option_items',
    'column' => 
    array (
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