<?php
require_once 'common.php';

app_check_login();

// 生成 CSRF 令牌（必须在输出前调用，写入 dp_csrf cookie）
app_csrf_token();

$post_id          = '';
$post_is_new      = false;
$post_state       = '';
$post_title       = '';
$post_desc        = '';
$post_content     = '';
$post_tags        = array();
$post_date        = '';
$post_time        = '';
$post_can_comment = '';
$error_msg        = '';
$succeed          = false;

if (isset($_POST['_IS_POST_BACK_'])) {
  // 写操作：先校验 CSRF 令牌
  app_verify_csrf();

  $post_id          = $_POST['id'];
  $post_state       = $_POST['state'];
  $post_title       = trim($_POST['title']);
  $post_desc        = trim($_POST['desc']);
  $post_content     = htmlspecialchars(trim($_POST['content']));
  $post_tags        = explode(',', trim($_POST['tags']));
  $post_date        = date("Y-m-d");
  $post_time        = date("H:i:s");
  $post_can_comment = ($_POST['can_comment'] == '0') ? '0' : '1';

  // 文章 ID 白名单校验，防止路径穿越
  if ($post_id != '' && !app_valid_post_id($post_id)) {
    Header("Location:post.php");
    exit;
  }

  if ($post_state != "delete" && $post_state != "draft" && $post_state != "publish") {
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
    $post_date = sprintf('%04d-%02d-%02d', $y, $mo, $d);
    $post_time = sprintf('%02d:%02d:%02d', $h, $mi, $s);
  }

  $post_tags_count = count($post_tags);

  for ($i = 0; $i < $post_tags_count; $i++) {
    $trim = trim($post_tags[$i]);
    if ($trim == '') {
      unset($post_tags[$i]);
    } else {
      $post_tags[$i] = $trim;
    }
  }

  reset($post_tags);

  if ($post_title == '') {
    $error_msg = '文章标题不能为空';
  } else if (!$date_valid) {
    $error_msg = '发布时间不合法';
  } else {
    // 文章 ID：新建时页面渲染阶段已预生成（hidden 字段带回），此处为无 ID 时的兜底
    if ($post_id == '') {
      $post_id = app_random_id(PATH_ROOT . '/data/posts/data', $post_title);
    }

    $file_path = PATH_ROOT . '/data/posts/data/' . $post_id . '.dat';
    $data_old  = false;

    if (is_file($file_path)) {
      // 已有数据文件：编辑已有文章
      $data_old = unserialize(file_get_contents($file_path));

      if (!is_array($data_old)) {
        Header("Location:post.php");
        exit;
      }

      $post_old_state = $data_old['state'];

      if ($post_old_state != $post_state) {
        $index_file = PATH_ROOT . '/data/posts/index/' . $post_old_state . '.php';

        require $index_file;

        unset($app_posts[$post_id]);

        file_put_contents(
          $index_file,
          "<?php\n\$app_posts = " . var_export($app_posts, true) . ";\n?>",
          LOCK_EX
        );
      }
    }

    $data = array(
      'id'          => $post_id,
      'state'       => $post_state,
      'title'       => $post_title,
      'tags'        => $post_tags,
      'date'        => $post_date,
      'time'        => $post_time,
      'can_comment'  => $post_can_comment,
      'desc'        => $post_desc,
    );

    // 编辑文章时更新最后修改时间；新建时发布时间即当前时间，不写 mod_time
    if (date("Y-m-d H:i:s") != $post_date . ' ' . $post_time) {
      $data['mod_time'] = date("Y-m-d H:i:s");
    }

    $index_file = PATH_ROOT . '/data/posts/index/' . $post_state . '.php';

    require $index_file;

    $app_posts[$post_id] = $data;

    uasort($app_posts, "post_sort");

    file_put_contents(
      $index_file,
      "<?php\n\$app_posts = " . var_export($app_posts, true) . ";\n?>",
      LOCK_EX
    );

    $data['content'] = $post_content;

    file_put_contents($file_path, serialize($data), LOCK_EX);

    $succeed = true;
  }
} else if (isset($_GET['id'])) {
  // 编辑入口：ID 白名单 + 数据文件存在性校验，防止路径穿越
  if (!app_valid_post_id($_GET['id'])) {
    Header("Location:post.php");
    exit;
  }

  $file_path = PATH_ROOT . '/data/posts/data/' . $_GET['id'] . '.dat';

  if (!is_file($file_path)) {
    Header("Location:post.php");
    exit;
  }

  $data = unserialize(file_get_contents($file_path));

  if (!is_array($data)) {
    Header("Location:post.php");
    exit;
  }

  $post_id      = $data['id'];
  $post_state   = $data['state'];
  $post_title   = $data['title'];
  $post_desc    = isset($data['desc']) ? $data['desc'] : '';
  $post_content = $data['content'];
  $post_tags    = $data['tags'];
  $post_date    = $data['date'];
  $post_time    = $data['time'];
  $post_can_comment = isset($data['can_comment']) ? $data['can_comment'] : '1';
} else {
  // 新建文章：先确定文章 ID（编辑器上传图片目录与 ID 保持一致），随表单 hidden 字段带回
  $post_is_new = true;
  $post_id = app_random_id(PATH_ROOT . '/data/posts/data');
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
    <?php if ($post_state == 'publish') { ?>
      <div class="updated">文章已发布。 <a href="<?php echo app_get_url('post', $post_id); ?>" class="link" target="_blank">查看文章</a></div>
    <?php } else { ?>
      <div class="updated">文章已保存到“草稿箱”。 <a href="post.php?state=draft">打开草稿箱</a></div>
    <?php } ?>
  <?php } else if ($error_msg != '') { ?>
    <div class="updated" style="background:#ffebe8;border:1px solid #c00;color:#c00;">保存失败：<?php echo $error_msg; ?></div>
  <?php } ?>
  <div class="admin_page_name">
    <?php if ($post_is_new) echo "撰写文章";
    else echo "编辑文章"; ?><a class="link_button" href="post.php">所有文章</a>
  </div>
  <div style="margin-bottom:20px;">
    <input name="title" type="text" class="edit_textbox" placeholder="在此输入标题" value="<?php echo htmlspecialchars($post_title); ?>" />
  </div>
  <div style="margin-bottom:20px;">
    <?php require 'editor.php'; ?>
    <?php editor(htmlspecialchars_decode($post_content), 'posts/' . $post_id); ?>
  </div>
  <div style="margin-bottom:20px;">
    <input name="desc" type="text" class="edit_textbox" placeholder="在此输入描述，将显示在网页SEO的description中" value="<?php echo htmlspecialchars($post_desc); ?>" />
  </div>
  <div style="margin-bottom:20px;">
    <input name="tags" type="text" class="edit_textbox" placeholder="在此输入标签，多个标签之间用逗号分隔" value="<?php echo htmlspecialchars(implode(',', $post_tags)); ?>" />
  </div>
  <div style="margin-bottom:20px;text-align:right">
    <div style="float:left">
      时间：
      <select name="year">
        <option value="">&nbsp;&nbsp;年</option>
        <?php $year = substr($post_date, 0, 4);
        for ($i = 1990; $i <= intval(date('Y')) + 10; $i++) { ?>
          <option value="<?php echo $i; ?>" <?php if ($year == $i) echo 'selected="selected";' ?>><?php echo $i; ?></option>
        <?php } ?>
      </select> -
      <select name="month">
        <option value="">月</option>
        <?php $month = substr($post_date, 5, 2);
        for ($i = 1; $i <= 12; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($month == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select> -
      <select name="day">
        <option value="">日</option>
        <?php $day = substr($post_date, 8, 2);
        for ($i = 1; $i <= 31; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($day == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select>&nbsp;
      <select name="hourse">
        <option value="">时</option>
        <?php $hourse = substr($post_time, 0, 2);
        for ($i = 0; $i <= 23; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($hourse == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select> :
      <select name="minute">
        <option value="">分</option>
        <?php $minute = substr($post_time, 3, 2);
        for ($i = 0; $i <= 59; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($minute == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select> :
      <select name="second">
        <option value="">秒</option>
        <?php $second = substr($post_time, 6, 2);
        for ($i = 0; $i <= 59; $i++) {
          $m = sprintf("%02d", $i); ?>
          <option value="<?php echo $m; ?>" <?php if ($second == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
        <?php } ?>
      </select>
    </div>
    评论：
    <select name="can_comment" style="margin-right:16px;">
      <option value="1" <?php if ($post_can_comment == '1') echo 'selected="selected";'; ?>>允许</option>
      <option value="0" <?php if ($post_can_comment == '0') echo 'selected="selected";'; ?>>禁用</option>
    </select>
    状态：
    <select name="state" style="width:100px;">
      <option value="draft" <?php if ($post_state == 'draft') echo 'selected="selected"'; ?>>草稿</option>
      <option value="publish" <?php if ($post_state == 'publish') echo 'selected="selected"'; ?>>发布</option>
    </select>
    <div style="clear:both;"></div>
  </div>
  <div style="text-align:right">
    <input type="hidden" name="id" value="<?php echo $post_id; ?>" />
    <input type="submit" name="save" value="保存" style="padding:6px 20px;" />
  </div>
</form>
<?php require 'foot.php' ?>