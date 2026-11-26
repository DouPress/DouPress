<?php
require_once 'common.php';

app_check_login();

// 生成 CSRF 令牌（必须在输出前调用，写入 dp_csrf cookie）
app_csrf_token();

$page_file        = '';
$page_path        = '';
$page_state       = '';
$page_title       = '';
$page_desc        = '';
$page_content     = '';
$page_date        = '';
$page_time        = '';
$page_can_comment = '';
$error_msg        = '';
$succeed          = false;

if (isset($_POST['_IS_POST_BACK_'])) {
  // 写操作：先校验 CSRF 令牌
  app_verify_csrf();

  $page_file        = $_POST['file'];
  $page_path        = $_POST['path'];
  $page_state       = $_POST['state'];
  $page_title       = trim($_POST['title']);
  $page_desc        = trim($_POST['desc']);
  $page_content     = htmlspecialchars(trim($_POST['content']));
  $page_date        = date("Y-m-d");
  $page_time        = date("H:i:s");
  $page_can_comment = ($_POST['can_comment'] == '0') ? '0' : '1';

  // 页面文件 ID 白名单校验，防止路径穿越
  if ($page_file != '' && !app_valid_post_id($page_file)) {
    Header("Location:page.php");
    exit;
  }

  if ($page_state != "delete" && $page_state != "draft" && $page_state != "publish") {
    Header("Location:index.php");
    exit;
  }

  // 发布时间合法性校验（checkdate），非法时间不落库
  $y  = ($_POST['year'] != '') ? intval($_POST['year']) : intval(date('Y'));
  $mo = ($_POST['month'] != '') ? intval($_POST['month']) : intval(date('n'));
  $d  = ($_POST['day'] != '') ? intval($_POST['day']) : intval(date('j'));
  $h  = ($_POST['hourse'] != '') ? intval($_POST['hourse']) : intval(date('G'));
  $mi = ($_POST['minute'] != '') ? intval($_POST['minute']) : intval(date('i'));
  $s  = ($_POST['second'] != '') ? intval($_POST['second']) : intval(date('s'));

  $date_valid = checkdate($mo, $d, $y) && $y >= 1990 && $y <= 2100
    && $h >= 0 && $h <= 23 && $mi >= 0 && $mi <= 59 && $s >= 0 && $s <= 59;

  if ($date_valid) {
    $page_date = sprintf('%04d-%02d-%02d', $y, $mo, $d);
    $page_time = sprintf('%02d:%02d:%02d', $h, $mi, $s);
  }

  $page_path_part  = explode('/', $page_path);
  $page_path_count = count($page_path_part);

  for ($i = 0; $i < $page_path_count; $i++) {
    $trim = trim($page_path_part[$i]);
    if ($trim == '') {
      unset($page_path_part[$i]);
    } else {
      $page_path_part[$i] = $trim;
    }
  }

  reset($page_path_part);

  $page_path = implode('/', $page_path_part);

  if ($page_title == '') {
    $error_msg = '页面标题不能为空';
  } else if ($page_path == '') {
    $error_msg = '页面路径不能为空';
  } else if (!app_valid_page_id($page_path)) {
    $error_msg = '页面路径只能包含字母、数字、中划线、下划线、点和斜杠';
  } else if (!$date_valid) {
    $error_msg = '发布时间不合法';
  } else {
    $page_old_path = '';

    // 页面文件 ID：新建时页面渲染阶段已预生成（hidden 字段带回），此处为无 ID 时的兜底
    if ($page_file == '') {
      $page_file = app_random_id(PATH_ROOT . '/data/pages/data', $page_title);
    }

    $file_path = PATH_ROOT . '/data/pages/data/' . $page_file . '.dat';
    $data_old  = false;

    if (is_file($file_path)) {
      // 已有数据文件：编辑已有页面
      $data_old = unserialize(file_get_contents($file_path));

      if (!is_array($data_old)) {
        Header("Location:page.php");
        exit;
      }

      $page_old_path  = $data_old['path'];
      $page_old_state = $data_old['state'];
    }

    // 路径唯一性校验（在改写任何索引之前，扫描全部状态索引），避免静默覆盖已有页面
    $path_conflict = false;
    foreach (array('publish', 'draft', 'delete') as $st) {
      $st_index = PATH_ROOT . '/data/pages/index/' . $st . '.php';
      if (!is_file($st_index)) {
        continue;
      }
      require $st_index;
      if (isset($app_pages[$page_path]) && $page_path != $page_old_path) {
        $path_conflict = true;
        break;
      }
    }

    if ($path_conflict) {
      $error_msg = '页面路径已存在：/' . htmlspecialchars($page_path);
    } else {
      if ($page_old_path != '' && ($page_old_state != $page_state || $page_old_path != $page_path)) {
        $index_file = PATH_ROOT . '/data/pages/index/' . $page_old_state . '.php';

        require $index_file;

        unset($app_pages[$page_old_path]);

        file_put_contents(
          $index_file,
          "<?php\n\$app_pages = " . var_export($app_pages, true) . ";\n?>",
          LOCK_EX
        );
      }

      $data = array(
        'file'        => $page_file,
        'path'        => $page_path,
        'state'       => $page_state,
        'title'       => $page_title,
        'date'        => $page_date,
        'time'        => $page_time,
        'can_comment' => $page_can_comment,
        'desc'        => $page_desc,
      );

      // 编辑页面时更新最后修改时间；新建时发布时间即当前时间，不写 mod_time
      if (date("Y-m-d H:i:s") != $page_date . ' ' . $page_time) {
        $data['mod_time'] = date("Y-m-d H:i:s");
      }

      $index_file = PATH_ROOT . '/data/pages/index/' . $page_state . '.php';

      require $index_file;

      $app_pages[$page_path] = $data;

      ksort($app_pages);

      file_put_contents(
        $index_file,
        "<?php\n\$app_pages = " . var_export($app_pages, true) . ";\n?>",
        LOCK_EX
      );

      $data['content'] = $page_content;

      file_put_contents($file_path, serialize($data), LOCK_EX);

      $succeed = true;
    }
  }
} else if (isset($_GET['file'])) {
  // 编辑入口：文件 ID 白名单 + 数据文件存在性校验，防止路径穿越
  if (!app_valid_post_id($_GET['file'])) {
    Header("Location:page.php");
    exit;
  }

  $file_path = PATH_ROOT . '/data/pages/data/' . $_GET['file'] . '.dat';

  if (!is_file($file_path)) {
    Header("Location:page.php");
    exit;
  }

  $data = unserialize(file_get_contents($file_path));

  if (!is_array($data)) {
    Header("Location:page.php");
    exit;
  }

  $page_file        = $data['file'];
  $page_path        = $data['path'];
  $page_state       = $data['state'];
  $page_title       = $data['title'];
  $page_desc        = isset($data['desc']) ? $data['desc'] : '';
  $page_content     = $data['content'];
  $page_date        = $data['date'];
  $page_time        = $data['time'];
  $page_can_comment = isset($data['can_comment']) ? $data['can_comment'] : '1';
} else {
  // 新建页面：先确定文件 ID（编辑器上传图片目录与 ID 保持一致），随表单 hidden 字段带回
  $page_file = app_random_id(PATH_ROOT . '/data/pages/data');
}
?>
<?php require 'head.php'; ?>

<script type="text/javascript">
  function empty_textbox_focus(target) {
    if (target.temp_value != undefined && target.value != target.temp_value)
      return;

    target.temp_value = target.value;
    target.value = '';
    target.style.color = '#000';
  }

  function empty_textbox_blur(target) {
    if (target.value == '') {
      target.style.color = '#888';
      target.value = target.temp_value;
    }
  }
</script>
<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
  <input type="hidden" name="_IS_POST_BACK_" value="" />
  <?php app_csrf_field(); ?>
  <?php if ($succeed) { ?>
    <?php if ($page_state == 'publish') { ?>
      <div class="updated">页面已发布。 <a href="<?php echo app_get_url('', $page_path); ?>" class="link" target="_blank">查看页面</a></div>
    <?php } else { ?>
      <div class="updated">页面已保存到“草稿箱”。 <a href="page.php?state=draft">打开草稿箱</a></div>
    <?php } ?>
  <?php } else if ($error_msg != '') { ?>
    <div class="updated" style="background:#ffebe8;border:1px solid #c00;color:#c00;">保存失败：<?php echo $error_msg; ?></div>
  <?php } ?>
  <div class="admin_page_name">
    <?php if ($page_path == '') {echo "创建页面";} else {echo "编辑页面";} ?><a class="link_button" href="page.php">所有页面</a>
  </div>
  <div style="margin-bottom:20px;">
    <input name="title" type="text" class="edit_textbox" placeholder="在此输入标题" value="<?php echo htmlspecialchars($page_title); ?>" />
  </div>
  <div style="margin-bottom:20px;">
    <?php require 'editor.php'; ?>
    <?php editor(htmlspecialchars_decode($page_content), 'pages/' . $page_file); ?>
  </div>
  <div style="margin-bottom:20px;">
    <input name="desc" type="text" class="edit_textbox" placeholder="在此输入描述，将显示在网页SEO的description中" value="<?php echo htmlspecialchars($page_desc); ?>" />
  </div>
  <div style="margin-bottom:20px;">
    <input name="path" type="text" class="edit_textbox" placeholder="在此输入页面路径，多级路径用半角斜杠(/)分割" value="<?php echo htmlspecialchars($page_path); ?>" />
  </div>
  <div style="margin-bottom:20px;text-align:right">
    <div style="float:left">
      时间：
      <select name="year">
        <option value="">&nbsp;&nbsp;年</option>
        <?php $year = substr($page_date, 0, 4);
        for ($i = 1990; $i <= intval(date('Y')) + 10; $i++) { ?>
          <option value="<?php echo $i; ?>" <?php if ($year == $i) echo 'selected="selected";' ?>><?php echo $i; ?></option>
        <?php } ?>
      </select> -
      <select name="month">
        <option value="">月</option>
        <?php $month = substr($page_date, 5, 2);
        for ($i = 1; $i <= 12; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($month == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select> -
      <select name="day">
        <option value="">日</option>
        <?php $day = substr($page_date, 8, 2);
        for ($i = 1; $i <= 31; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($day == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select>&nbsp;
      <select name="hourse">
        <option value="">时</option>
        <?php $hourse = substr($page_time, 0, 2);
        for ($i = 0; $i <= 23; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($hourse == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select> :
      <select name="minute">
        <option value="">分</option>
        <?php $minute = substr($page_time, 3, 2);
        for ($i = 0; $i <= 59; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($minute == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select> :
      <select name="second">
        <option value="">秒</option>
        <?php $second = substr($page_time, 6, 2);
        for ($i = 0; $i <= 59; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($second == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select>
    </div>
    评论：
    <select name="can_comment" style="margin-right:16px;">
      <option value="1" <?php if ($page_can_comment == '1') echo 'selected="selected";'; ?>>允许</option>
      <option value="0" <?php if ($page_can_comment == '0') echo 'selected="selected";'; ?>>禁用</option>
    </select>
    状态：
    <select name="state" style="width:100px;">
      <option value="draft" <?php if ($page_state == 'draft') echo 'selected="selected"'; ?>>草稿</option>
      <option value="publish" <?php if ($page_state == 'publish') echo 'selected="selected"'; ?>>发布</option>
    </select>
  </div>
  <div style="text-align:right">
    <input type="hidden" name="file" value="<?php echo $page_file; ?>" />
    <input type="submit" name="save" value="保存" style="padding:6px 20px;" />
  </div>
</form>
<?php require 'foot.php' ?>