<?php

namespace addons\qiniu;

use addons\qiniu\library\Auth;
use fast\Http;
use think\Addons;

/**
 * 七牛上传插件
 */
class Qiniu extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 上传初始化时
     */
    public function uploadConfigInit(&$upload)
    {
        $config = $this->getConfig();

        $policy = array(
            'saveKey' => ltrim($config['savekey'], '/'),
        );
        //如果启用服务端回调
        if ($config['notifyenabled']) {
            $policy = array_merge($policy, [
                'callbackUrl'  => $config['notifyurl'],
                'callbackBody' => 'filename=$(fname)&key=$(key)&imageInfo=$(imageInfo)&filesize=$(fsize)&admin=$(x:admin)&user=$(x:user)'
            ]);
        }

        if ($config['uploadmode'] == 'client') {
            $auth = new Auth($config['app_key'], $config['secret_key']);
            $multipart['token'] = $auth->uploadToken($config['bucket'], null, $config['expire'], $policy);
            $multipart['x:admin'] = (int)session('admin.id');
            $multipart['x:user'] = (int)cookie('uid');
            $upload = [
                'cdnurl'    => $config['cdnurl'],
                'uploadurl' => $config['uploadurl'],
                'bucket'    => $config['bucket'],
                'maxsize'   => $config['maxsize'],
                'mimetype'  => $config['mimetype'],
                'multipart' => $multipart,
                'multiple'  => $config['multiple'] ? true : false,
            ];
        } else {
            $upload = array_merge($upload, [
                'cdnurl'    => $config['cdnurl'],
                'uploadurl' => addon_url('qiniu/index/upload'),
                'maxsize'   => $config['maxsize'],
                'mimetype'  => $config['mimetype'],
                'multiple'  => $config['multiple'] ? true : false,
            ]);
        }
    }

    /**
     * 附件删除后
     */
    public function uploadDelete($attachment)
    {
        $config = $this->getConfig();
        if ($attachment['storage'] == 'qiniu' && isset($config['syncdelete']) && $config['syncdelete']) {
            $auth = new Auth($config['app_key'], $config['secret_key']);
            $entry = $config['bucket'] . ':' . ltrim($attachment->url, '/');
            $encodedEntryURI = $auth->base64_urlSafeEncode($entry);
            $url = 'http://rs.qiniu.com/delete/' . $encodedEntryURI;
            $headers = $auth->authorization($url);
            //删除云储存文件
            $ret = Http::sendRequest($url, [], 'POST', [CURLOPT_HTTPHEADER => ['Authorization: ' . $headers['Authorization']]]);
            //如果是服务端中转，还需要删除本地文件
            if ($config['uploadmode'] == 'server') {
                $filePath = ROOT_PATH . 'public' . str_replace('/', DS, $attachment->url);
                if ($filePath) {
                    @unlink($filePath);
                }
            }
        }
        return true;
    }

}
