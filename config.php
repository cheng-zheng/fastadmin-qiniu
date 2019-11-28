<?php

return array (
  0 => 
  array (
    'name' => 'app_key',
    'title' => 'app_key',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'your app_key',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请在个人中心 > 密钥管理中获取 > AK',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'secret_key',
    'title' => 'secret_key',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'your secret_key',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请在个人中心 > 密钥管理中获取 > SK',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'bucket',
    'title' => 'bucket',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'your bucket',
    'rule' => 'required',
    'msg' => '',
    'tip' => '存储空间名称',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'uploadurl',
    'title' => '上传接口地址',
    'type' => 'select',
    'content' => 
    array (
      'https://upload-z0.qiniup.com' => '华东 https://upload-z0.qiniup.com',
      'https://upload-z1.qiniup.com' => '华北 https://upload-z1.qiniup.com',
      'https://upload-z2.qiniup.com' => '华南 https://upload-z2.qiniup.com',
      'https://upload-na0.qiniup.com' => '北美 https://upload-na0.qiniup.com',
      'https://upload-as0.qiniup.com' => '东南亚 https://upload-as0.qiniup.com',
    ),
    'value' => 'https://upload-z2.qiniup.com',
    'rule' => 'required',
    'msg' => '',
    'tip' => '推荐选择最近的地址',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'cdnurl',
    'title' => 'CDN地址',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'http://yourbucket.bkt.clouddn.com',
    'rule' => 'required',
    'msg' => '',
    'tip' => '未绑定CDN的话可使用七牛分配的测试域名',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => 'notifyenabled',
    'title' => '启用服务端回调',
    'type' => 'bool',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => '',
    'msg' => '',
    'tip' => '本地开发请禁用服务端回调',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'notifyurl',
    'title' => '回调通知地址',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'http://www.yoursite.com/addons/qiniu/index/notify',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'uploadmode',
    'title' => '上传模式',
    'type' => 'select',
    'content' => 
    array (
      'client' => '客户端直传(速度快,无备份)',
      'server' => '服务器中转(占用服务器带宽,有备份)',
    ),
    'value' => 'server',
    'rule' => '',
    'msg' => '',
    'tip' => '启用服务器中转时务必配置操作员和密码',
    'ok' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'savekey',
    'title' => '保存文件名',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '/uploads/$(year)$(mon)$(day)/$(etag)$(ext)',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'name' => 'expire',
    'title' => '上传有效时长',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '600',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  10 => 
  array (
    'name' => 'maxsize',
    'title' => '最大可上传',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '10M',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  11 => 
  array (
    'name' => 'mimetype',
    'title' => '可上传后缀格式',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  12 => 
  array (
    'name' => 'multiple',
    'title' => '多文件上传',
    'type' => 'bool',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  13 => 
  array (
    'name' => 'syncdelete',
    'title' => '附件删除时是否同步删除文件',
    'type' => 'bool',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  14 => 
  array (
    'name' => '__tips__',
    'title' => '温馨提示',
    'type' => '',
    'content' => 
    array (
    ),
    'value' => '在使用之前请注册七牛账号并进行认证，注册链接:<a href="https://portal.qiniu.com/signup?code=3l79xtos9w9qq" target="_blank">https://portal.qiniu.com/signup?code=3l79xtos9w9qq</a>',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
