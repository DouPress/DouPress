<?php
/**
 * 图片上传接口（供编辑器 pell 上传调用）
 * 返回 JSON：{msg, code, data:{errFiles:[], succMap:{}}}
 * 存储结构：data/uploads/{posts|pages}/{文章或页面ID}/{md5}.ext（目录与内容 ID 一致）
 */
require_once __DIR__ . '/common.php';
app_check_login();

header('Content-Type: application/json; charset=utf-8');

function app_upload_json($msg, $code, $data = null)
{
  echo json_encode(array('msg' => $msg, 'code' => $code, 'data' => $data));
  exit;
}

// CSRF：令牌由编辑器拼在上传 URL 的 _dp_token 传入（攻击者无法预知令牌，跨站请求无法通过）
if (empty($_COOKIE['dp_csrf']) || empty($_GET['_dp_token'])
  || !hash_equals($_COOKIE['dp_csrf'], $_GET['_dp_token'])) {
  app_upload_json('非法请求（CSRF 校验失败），请刷新页面后重试', -1);
}

// 上传目标目录：与文章/页面 ID 一致，编辑器传入 target=posts/{id} 或 pages/{id}
$target = isset($_GET['target']) ? trim($_GET['target']) : '';
if (!preg_match('#^(posts|pages)/[a-z0-9]{1,8}$#', $target)) {
  app_upload_json('上传目录参数缺失或不合法', -1);
}
$dir = PATH_ROOT . '/data/uploads/' . $target;

$allow_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
$max_size  = 10 * 1024 * 1024;

if (empty($_FILES['file'])) {
  app_upload_json('没有收到上传文件', -1);
}

// 统一为多文件数组（编辑器默认字段名为 file[]）
$files = $_FILES['file'];
if (!is_array($files['name'])) {
  $files = array(
    'name'     => array($files['name']),
    'type'     => array($files['type']),
    'tmp_name' => array($files['tmp_name']),
    'error'    => array($files['error']),
    'size'     => array($files['size']),
  );
}

$succ_map = array();
$err_files = array();

foreach ($files['name'] as $i => $name) {
  if ($files['error'][$i] !== UPLOAD_ERR_OK) {
    $err_files[] = $name;
    continue;
  }
  if ($files['size'][$i] > $max_size) {
    $err_files[] = $name;
    continue;
  }
  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, $allow_ext)) {
    $err_files[] = $name;
    continue;
  }
  // 内容校验为真实图片（webp 在老版本 PHP 的 getimagesize 不支持，仅做扩展名白名单）
  $info = @getimagesize($files['tmp_name'][$i]);
  if ($info === false && $ext !== 'webp') {
    $err_files[] = $name;
    continue;
  }

  if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
    $err_files[] = $name;
    continue;
  }

  $file_name = md5(microtime() . $name . $i) . '.' . $ext;
  if (!@move_uploaded_file($files['tmp_name'][$i], $dir . '/' . $file_name)) {
    $err_files[] = $name;
    continue;
  }

  $succ_map[$name] = app_site_link(false) . '/data/uploads/' . $target . '/' . $file_name;
}

if (empty($succ_map)) {
  app_upload_json('上传失败：文件类型不支持（仅限 jpg/png/gif/webp）或超过 10MB', -1);
}

app_upload_json('', 0, array('errFiles' => $err_files, 'succMap' => $succ_map));
