Fastadmin 七牛云上传插件
放在addnos目录下

修改application/extra/addnos.php

```php
    'upload_config_init' => 
        array (
          0 => 'qiniu',
        ),
        'upload_delete' => 
        array (
          0 => 'qiniu',
        ),
```

修改public/assets/addnos.js

```javascript
    //修改上传的接口调用
    require(['upload'], function (Upload) {
        var _onUploadResponse = Upload.events.onUploadResponse;
        Upload.events.onUploadResponse = function (response) {
            try {
                var ret = typeof response === 'object' ? response : JSON.parse(response);
                if (ret.hasOwnProperty("code") && ret.hasOwnProperty("data")) {
                    return _onUploadResponse.call(this, response);
                } else if (ret.hasOwnProperty("key") && !ret.hasOwnProperty("err_code")) {
                    ret.code = 1;
                    ret.data = {
                        url: '/' + ret.key
                    };
                    return _onUploadResponse.call(this, JSON.stringify(ret));
                }
            } catch (e) {
            }
            return _onUploadResponse.call(this, response);
    
        };
    });
```