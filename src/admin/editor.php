<?php
/**
 * 编辑器（pell 极轻量富文本，约 4KB、零依赖，自托管于 admin/editor/pell/）
 * - 富文本模式：contenteditable 即时排版（不支持 Markdown 语法）
 * - 可一键切换纯文本模式（原生 textarea），偏好记忆在浏览器 localStorage
 * - 两种模式均支持上传图片（admin/upload.php），成功后自动插入输入框内
 * - 上传目录与文章/页面 ID 一致（target=posts/{id} 或 pages/{id}），新建时 ID 已在页面渲染阶段预生成
 */
function editor($content, $upload_target = '') { ?>
  <div id="editor_wrap">
    <div id="editor_bar">
      <label><input type="checkbox" id="editor_rich_toggle" /> 富文本</label>
      <span type="button" id="editor_upload_btn" class="link_button">上传图片</span>
      <input type="file" id="editor_upload_input" multiple accept="image/jpeg,image/png,image/gif,image/webp" style="display:none" />
      <span id="editor_upload_status"></span>
    </div>

    <div id="pell" class="edit_textarea" style="display:none;"></div>
    <!-- 提交内容承载域：富文本模式提交前同步，无 JS 时退化为纯文本编辑 -->
    <textarea id="editor_source" name="content" class="edit_textarea" style="display:block;width:100%;box-sizing:border-box;"><?php echo htmlspecialchars($content); ?></textarea>

    <style>
        #pell .pell-source { width: 100%; height: 540px; box-sizing: border-box; font-family: Menlo, Consolas, monospace; font-size: 13px; resize: vertical; }
        #pell .pell-actionbar.pell-source-on button { opacity: .45; }
        #editor_bar { margin-bottom: 8px; }
        #editor_upload_btn { margin-left: 12px; }
        #editor_upload_status { margin-left: 8px; color: #888; }
        #pell img { max-width: 100%; }
        .pell { border: 1px solid rgba(10, 10, 10, 0.1); box-sizing: border-box; }
        .pell-content { box-sizing: border-box; height: 367px; outline: 0; overflow-y: auto; padding: 10px; }
        .pell-actionbar { position: sticky; background-color: #FFF; border-bottom: 1px solid rgba(10, 10, 10, 0.1); }
        .pell-button {
            background-color: transparent;
            border: none;
            cursor: pointer;
            height: 30px;
            outline: 0;
            width: 30px;
            border-radius: 3px;
            vertical-align: bottom; }
        .pell-button:hover { background:#bc0000; color:#fff; }
        .pell-button-selected { background-color: #F0F0F0; }

    </style>
    <script src="editor/pell/pell.min.js"></script>
    <script>
      var app_pell_element = null;
      var app_pell_bar = null;
      var app_source_ta = null;
      var app_source_mode = false;
      var app_editor_html = '';
      var app_csrf_token = '<?php echo app_csrf_token(); ?>';
      var app_upload_target = '<?php echo $upload_target; ?>';

      function app_editor_is_rich() {
        return document.getElementById('editor_rich_toggle').checked;
      }

      // 当前编辑器的真实内容（源码视图下取源码框内容）
      function app_editor_current_html() {
        return app_source_mode && app_source_ta ? app_source_ta.value : app_pell_element.content.innerHTML;
      }

      // 查看/编辑 HTML 源码视图（按钮 "</>"）
      function app_toggle_source() {
        if (!app_source_mode) {
          app_source_ta.value = app_pell_element.content.innerHTML;
          app_pell_element.content.style.display = 'none';
          app_source_ta.style.display = 'block';
          app_source_ta.focus();
          app_source_mode = true;
        } else {
          app_pell_element.content.innerHTML = app_source_ta.value;
          app_editor_html = app_source_ta.value;
          app_pell_element.content.style.display = '';
          app_source_ta.style.display = 'none';
          app_source_mode = false;
        }
        if (app_pell_bar) {
          app_pell_bar.className = 'pell-actionbar' + (app_source_mode ? ' pell-source-on' : '');
        }
      }

      function app_apply_editor_mode() {
        var rich = app_editor_is_rich();
        document.getElementById('pell').style.display = rich ? 'block' : 'none';
        document.getElementById('editor_source').style.display = rich ? 'none' : 'block';
        if (!rich) {
          document.getElementById('editor_source').value = app_editor_current_html();
        }
      }

      // 在富文本/纯文本中插入图片
      function app_insert_image(url) {
        if (app_editor_is_rich() && !app_source_mode) {
          var content = app_pell_element.content;
          content.focus();
          if (!document.execCommand('insertImage', false, url)) {
            document.execCommand('insertHTML', false, '<img src="' + url + '" alt="" />');
          }
          app_editor_html = content.innerHTML;
        } else {
          var ta = app_source_mode && app_source_ta ? app_source_ta : document.getElementById('editor_source');
          var start = ta.selectionStart || 0;
          var end = ta.selectionEnd || 0;
          var tag = '<img src="' + url + '" alt="" />';
          ta.value = ta.value.substring(0, start) + tag + ta.value.substring(end);
          ta.selectionStart = ta.selectionEnd = start + tag.length;
          ta.focus();
        }
      }

      // 上传图片到 upload.php（登录态 + CSRF 令牌），成功后自动插入
      function app_upload_files(files) {
        var status = document.getElementById('editor_upload_status');
        var fd = new FormData();
        for (var i = 0; i < files.length; i++) {
          fd.append('file[]', files[i]);
        }
        status.textContent = '上传中…';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php?_dp_token=' + encodeURIComponent(app_csrf_token)
          + '&target=' + encodeURIComponent(app_upload_target));
        xhr.onreadystatechange = function () {
          if (xhr.readyState !== 4) return;
          var res;
          try {
            res = JSON.parse(xhr.responseText);
          } catch (e) {
            status.textContent = '上传失败：服务器返回异常';
            return;
          }
          if (!res || res.code !== 0 || !res.data || !res.data.succMap) {
            status.textContent = (res && res.msg) ? res.msg : '上传失败';
            return;
          }
          for (var name in res.data.succMap) {
            app_insert_image(res.data.succMap[name]);
          }
          status.textContent = (res.data.errFiles && res.data.errFiles.length)
            ? '部分失败：' + res.data.errFiles.join(', ')
            : '';
          document.getElementById('editor_upload_input').value = '';
        };
        xhr.send(fd);
      }

      window.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('editor_rich_toggle');
        var textarea = document.getElementById('editor_source');

        toggle.checked = localStorage.getItem('dp_editor_rich') !== '0';

        app_pell_element = pell.init({
          element: document.getElementById('pell'),
          defaultParagraphSeparator: 'p',
          actions: [
            'bold', 'italic', 'underline', 'strikethrough',
            'heading1', 'heading2', 'paragraph',
            'quote', 'olist', 'ulist', 'code', 'line',
            'link', 'image',
            { // 查看/编辑 HTML 源码
              icon: '&lt;/&gt;',
              title: '查看代码',
              result: function () { app_toggle_source(); }
            }
          ],
          onChange: function (html) {
            app_editor_html = html;
          }
        });

        // 源码视图编辑域（HTML）
        app_source_ta = document.createElement('textarea');
        app_source_ta.className = 'pell-source';
        app_source_ta.style.display = 'none';
        document.getElementById('pell').appendChild(app_source_ta);
        app_pell_bar = document.querySelector('#pell .pell-actionbar');

        // 源码视图下点击其它格式按钮：先应用源码退出视图，再执行被点击的操作
        app_pell_bar.addEventListener('click', function (e) {
          if (app_source_mode && e.target.tagName === 'BUTTON' && e.target.textContent !== '</>') {
            app_toggle_source();
          }
        });

        // 初始内容：textarea 承载，富文本初始化后接管
        app_editor_html = textarea.value;
        app_pell_element.content.innerHTML = textarea.value;

        // 模式切换
        toggle.addEventListener('change', function () {
          localStorage.setItem('dp_editor_rich', toggle.checked ? '1' : '0');
          if (toggle.checked) {
            // 切回富文本：把纯文本内容同步进编辑器，并复位源码视图
            app_editor_html = textarea.value;
            app_pell_element.content.innerHTML = textarea.value;
            app_pell_element.content.style.display = '';
            app_source_ta.style.display = 'none';
            app_source_mode = false;
            if (app_pell_bar) {
              app_pell_bar.className = 'pell-actionbar';
            }
          }
          app_apply_editor_mode();
        });

        // 上传图片（两种模式共用）
        document.getElementById('editor_upload_btn').addEventListener('click', function () {
          document.getElementById('editor_upload_input').click();
        });
        document.getElementById('editor_upload_input').addEventListener('change', function () {
          if (this.files && this.files.length) {
            app_upload_files(this.files);
          }
        });

        // 表单提交前同步富文本内容
        var forms = document.getElementsByTagName('form');
        for (var i = 0; i < forms.length; i++) {
          forms[i].addEventListener('submit', function () {
            if (app_editor_is_rich()) {
              textarea.value = app_editor_current_html();
            }
          });
        }

        app_apply_editor_mode();
      });
    </script>
  </div>
<?php } ?>
