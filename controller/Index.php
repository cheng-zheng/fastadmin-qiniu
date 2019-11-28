<?php

namespace addons\qiniu\controller;

use addons\qiniu\library\Auth;
use app\common\model\Attachment;
use think\addons\Controller;
use think\Config;

/**
 * 七牛管理
 *
 */
class Index extends Controller
{
    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }

    /**
     * 上传接口
     */
    public function upload()
    {
        Config::set('default_return_type', 'json');
        if (!session('admin') && !$this->auth->id) {
            $this->error("请登录后再进行操作");
        }
        $config = get_addon_config('qiniu');

        $file = $this->request->file('file');
        if (!$file || !$file->isValid()) {
            $this->error("请上传有效的文件");
        }
        $fileInfo = $file->getInfo();

        $filePath = $file->getRealPath() ?: $file->getPathname();

        preg_match('/(\d+)(\w+)/', $config['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$config['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);

        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $md5 = md5_file($filePath);
        $search = ['$(year)', '$(mon)', '$(day)', '$(etag)', '$(ext)'];
        $replace = [date("Y"), date("m"), date("d"), $md5, '.' . $suffix];
        $object = ltrim(str_replace($search, $replace, $config['savekey']), '/');

        $mimetypeArr = explode(',', strtolower($config['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //检查文件大小
        if (!$file->checkSize($size)) {
            $this->error("起过最大可上传文件限制");
        }

        //验证文件后缀
        if ($config['mimetype'] !== '*' &&
            (
                !in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0] . '/', $config['mimetype']) !== false && (!in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr)))
            )
        ) {
            $this->error(__('上传格式限制'));
        }

        $savekey = '/' . $object;

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //先上传到本地
        $splInfo = $file->move(ROOT_PATH . '/public' . $uploadDir, $fileName);
        if ($splInfo) {
            $extparam = $this->request->post();
            $filePath = $splInfo->getRealPath() ?: $splInfo->getPathname();

            $sha1 = sha1_file($filePath);
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'admin_id'    => session('admin.id'),
                'user_id'     => $this->auth->id,
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $fileInfo['type'],
                'url'         => $uploadDir . $splInfo->getSaveName(),
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => $sha1,
                'extparam'    => json_encode($extparam),
            );
            $attachment = Attachment::create(array_filter($params), true);
            $policy = array(
                'saveKey' => ltrim($savekey, '/'),
            );
            $auth = new Auth($config['app_key'], $config['secret_key']);
            $token = $auth->uploadToken($config['bucket'], null, $config['expire'], $policy);
            $multipart = [
                ['name' => 'token', 'contents' => $token],
                [
                    'name'     => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => $fileName,
                ]
            ];
            try {
                $client = new \GuzzleHttp\Client();
                $res = $client->request('POST', $config['uploadurl'], [
                    'multipart' => $multipart
                ]);
                $code = $res->getStatusCode();
                //成功不做任何操作
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $attachment->delete();
                unlink($filePath);
                $this->error("上传失败");
            }

            $url = '/' . $object;

            //上传成功后将存储变更为qiniu
            $attachment->storage = 'qiniu';
            $attachment->save();

            $this->success("上传成功", null, ['url' => $url]);
        } else {
            $this->error('上传失败');
        }
        return;
    }

    /**
     * 通知回调
     */
    public function notify()
    {
        $config = get_addon_config('qiniu');
        $auth = new Auth($config['app_key'], $config['secret_key']);
        $contentType = 'application/x-www-form-urlencoded';
        $authorization = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$authorization && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authorization = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        }

        $url = $config['notifyurl'];
        $body = file_get_contents('php://input');
        $ret = $auth->verifyCallback($contentType, $authorization, $url, $body);
        if ($ret) {
            parse_str($body, $arr);
            $admin_id = isset($arr['admin']) ? $arr['admin'] : 0;
            $user_id = isset($arr['user']) ? $arr['user'] : 0;
            $imageInfo = json_decode($arr['imageInfo'], true);
            $params = array(
                'admin_id'    => (int)$admin_id,
                'user_id'     => (int)$user_id,
                'filesize'    => $arr['filesize'],
                'imagewidth'  => isset($imageInfo['width']) ? $imageInfo['width'] : 0,
                'imageheight' => isset($imageInfo['height']) ? $imageInfo['height'] : 0,
                'imagetype'   => isset($imageInfo['format']) ? $imageInfo['format'] : '',
                'imageframes' => 1,
                'mimetype'    => "image/" . (isset($imageInfo['format']) ? $imageInfo['format'] : ''),
                'extparam'    => '',
                'url'         => '/' . $arr['key'],
                'uploadtime'  => time(),
                'storage'     => 'qiniu'
            );
            Attachment::create($params);
            return json(['ret' => 'success', 'code' => 1, 'data' => ['url' => $params['url']]]);
        }
        return json(['ret' => 'failed']);
    }
}
