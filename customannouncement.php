<?php
/*
Plugin Name: Easy Custom Announcement
Plugin URI: https://www.jingxialai.com/4205.html
Description: 一个自定义公告插件，可以在后台设置公告内容和显示位置。（新增分类文章）
Version: 3.0
Author: Summer
License: GPL License
*/

// 添加菜单到后台
function custom_announcement_menu() {
    add_menu_page(
        'Custom Announcement',
        '公告设置',
        'manage_options',
        'custom-announcement',
        'custom_announcement_page'
    );
}

add_action('admin_menu', 'custom_announcement_menu');

// 插件设置页面
function custom_announcement_page() {
    if (isset($_POST['custom_announcement_submit'])) {
        $announcement_text = wp_kses_post($_POST['announcement_text']);
        $announcement_position = sanitize_text_field($_POST['announcement_position']);
        $selected_category = isset($_POST['selected_category']) ? absint($_POST['selected_category']) : 0;
        $selected_page = isset($_POST['selected_page']) ? absint($_POST['selected_page']) : 0;
        $display_announcement = isset($_POST['display_announcement']) ? 1 : 0; // 新增的复选框

        update_option('custom_announcement_text', $announcement_text);
        update_option('custom_announcement_position', $announcement_position);
        update_option('custom_announcement_category', $selected_category); //特定分类
        update_option('custom_announcement_page', $selected_page);  //特定页面
        update_option('display_custom_announcement', $display_announcement); // 保存是否显示公告的选项

        // 新增代码：保存是否在当前分类的所有文章页面显示的选项
        $display_on_all_posts_in_category = isset($_POST['display_on_all_posts_in_category']) ? 1 : 0;
        update_option('display_on_all_posts_in_category', $display_on_all_posts_in_category);//分类文章

        // 新增代码：保存是否在所有文章页面显示的选项
        $display_on_all_posts = isset($_POST['display_on_all_posts']) ? 1 : 0;
        update_option('display_on_all_posts', $display_on_all_posts);

        // 新增代码：保存是否在全站所有页面显示的选项
        $display_on_all_pages = isset($_POST['display_on_all_pages']) ? 1 : 0;
        update_option('display_on_all_pages', $display_on_all_pages);

        // 新增代码：保存按钮颜色选项
        $button_color = sanitize_text_field($_POST['button_color']);
        update_option('custom_announcement_button_color', $button_color);

        // 新增代码：保存弹窗边框颜色选项
        $popup_border_color = sanitize_text_field($_POST['popup_border_color']);
        update_option('custom_announcement_popup_border_color', $popup_border_color);


        // 新增代码：今日不再显示
        if (isset($_POST['dont_show_today'])) {
            $dont_show_today_timestamp = current_time('timestamp');
            update_option('custom_announcement_dont_show_today', $dont_show_today_timestamp);
        } else {
            delete_option('custom_announcement_dont_show_today');
        }

        echo '<div class="notice notice-success is-dismissible"><p>设置已保存。</p></div>';
    }

    $announcement_text = get_option('custom_announcement_text', '');
    $announcement_position = get_option('custom_announcement_position', 'homepage');
    $selected_category = get_option('custom_announcement_category', 0);
    $selected_page = get_option('custom_announcement_page', 0);
    $display_announcement = get_option('display_custom_announcement', 1);
    $display_on_all_posts = get_option('display_on_all_posts', 0);
    $display_on_all_posts_in_category = get_option('display_on_all_posts_in_category', 0);
    $display_on_all_pages = get_option('display_on_all_pages', 0);
    $dont_show_today_timestamp = get_option('custom_announcement_dont_show_today', 0);
    $dont_show_today = ($dont_show_today_timestamp > strtotime('today')) ? true : false;
    $button_color = get_option('custom_announcement_button_color', '#4CAF50'); // 默认按钮颜色


    $categories = get_categories();
    $pages = get_pages();
    ?>
    <div class="wrap">
        <h1>公告设置</h1>
        <div class="settings-container">
        <form method="post" action="">
            <label for="announcement_text">公告内容：</label>
            <textarea name="announcement_text" rows="5" cols="50"><?php echo esc_textarea($announcement_text); ?></textarea>
            <br>
            <label for="announcement_position">显示位置：</label>
            <select name="announcement_position">
                <option value="homepage" <?php selected($announcement_position, 'homepage'); ?>>网站首页</option>
                <option value="category" <?php selected($announcement_position, 'category'); ?>>特定分类</option>
                <option value="page" <?php selected($announcement_position, 'page'); ?>>特定页面</option>
                <option value="all_pages" <?php selected($announcement_position, 'all_pages'); ?>>全站所有页面</option>
            </select>
            <br>
            <label for="selected_category">选择分类：</label>
            <select name="selected_category">
                <option value="0">不选择</option>
                <?php
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->term_id) . '" ' . selected($selected_category, $category->term_id, false) . '>' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>
            <br>
            <label for="selected_page">选择页面：</label>
            <select name="selected_page">
                <option value="0">不选择</option>
                <?php
                foreach ($pages as $page) {
                    echo '<option value="' . esc_attr($page->ID) . '" ' . selected($selected_page, $page->ID, false) . '>' . esc_html($page->post_title) . '</option>';
                }
                ?>
            </select>
            <br>
            <label>
                <input type="checkbox" name="display_announcement" <?php checked($display_announcement, 1); ?> />
                在网站上显示公告
            </label>
            <br>
        <label>
            <input type="checkbox" name="display_on_all_posts" <?php checked($display_on_all_posts, 1); ?> />
            在所有文章页面显示公告
        </label>
      <br>
      <label>
        <input type="checkbox" name="display_on_all_posts_in_category" <?php checked(get_option('display_on_all_posts_in_category', 0), 1); ?> />
    在当前选择的分类文章页面显示公告
      </label>
      <br>
           <label for="button_color">公告按钮颜色：</label>
            <input type="text" name="button_color" value="<?php echo esc_attr($button_color); ?>" placeholder="#4CAF50" />
            <p class="description">输入按钮的颜色代码，例如 #4CAF50。</p>
            <label for="popup_border_color">公告边框颜色：</label>
            <input type="text" name="popup_border_color" value="<?php echo esc_attr(get_option('custom_announcement_popup_border_color', '#1E88E5')); ?>" placeholder="#1E88E5" />
             <p class="description">输入边框的颜色代码，例如 #1E88E5。</p>
             <br>
            <input type="submit" name="custom_announcement_submit" class="button-primary" value="保存设置">
            <br><br>
        1、如果你有其他公告插件或者主题有公告功能，可能会造成冲突无法正常使用.<br>
        <font color="#FF3300">2、当前选择的分类文章和全站所有文章不要同时选上.</font><br>
            <font color="#0000CC">3、如果显示位置选择了全站所有页面，就不用勾选下面的2个框了.</font><br>
        4、支持html格式，如果里面有图片自行限制下图片尺寸.<p>
            问题反馈邮箱：me@jingxialai.com
                    </form>
            <div class="custom-section">
                <h3>额外说明</h3>
                <p>Web颜色参考：<a href="https://www.jingxialai.com/webcolors.html" target="_blank">Webcolors</a></p>
                <img src="https://ypwenjian.jingxialai.com/jingxialai/2023/11/20231124083449222.jpg" height="150" width="300" >
            </div>
        </div>
    </div>
        <style>
        .settings-container {
            display: flex;
            justify-content: space-between;
        }

        .settings-form {
            width: 55%; 
        }

        .custom-section {
            width: 40%; 
        }
    </style>
    <?php
}

// 在前端显示弹窗公告
function display_custom_announcement() {
    $display_announcement = get_option('display_custom_announcement', 1);
    $display_on_all_posts = get_option('display_on_all_posts', 0);
    $display_on_all_posts_in_category = get_option('display_on_all_posts_in_category', 0);
    $display_on_all_pages = get_option('display_on_all_pages', 0);
    $button_color = get_option('custom_announcement_button_color', '#4CAF50'); // 默认按钮颜色
    $popup_border_color = get_option('custom_announcement_popup_border_color', '#1E88E5'); // 默认边框颜色

    if ((!$display_announcement && !$display_on_all_posts && !$display_on_all_posts_in_category && !$display_on_all_pages) || check_dont_show_today_cookie()) {
        return; // 如果公告显示已关闭或用户选择今天不显示，则返回
    }

    $announcement_text = get_option('custom_announcement_text', '');
    $announcement_position = get_option('custom_announcement_position', 'homepage');
    $selected_category = get_option('custom_announcement_category', 0);
    $selected_page = get_option('custom_announcement_page', 0);

    if ($announcement_text) {
        $display_popup = false;

        if ($announcement_position === 'homepage' && is_front_page()) {
            $display_popup = true;
        } elseif ($announcement_position === 'category' && (is_category($selected_category) || ($display_on_all_posts_in_category && in_category($selected_category)))) {
            $display_popup = true;
        } elseif ($announcement_position === 'page' && is_page($selected_page)) {
            $display_popup = true;
        } elseif ($announcement_position === 'all_pages') {
            $display_popup = true;
        } elseif ($display_on_all_posts && is_single()) {
            $display_popup = true;
        }

        if ($display_popup) {
            ?>
            <div id="custom-announcement-popup" class="custom-announcement-popup">
                <div class="custom-announcement-popup-content" style="border-color: <?php echo esc_attr($button_color); ?>;">
                    <div class="custom-announcement-popup-text"><?php echo $announcement_text; ?></div>
                    <label>
                        <input type="checkbox" id="dont-show-today-checkbox" />
                        今日不再弹出
                    </label>
                    <button class="custom-announcement-popup-close" onclick="closeCustomAnnouncementPopup()" style="background-color: <?php echo esc_attr($button_color); ?>;">点击关闭公告</button>
                </div>
            </div>
<script>
    function closeCustomAnnouncementPopup() {
        if (document.getElementById('dont-show-today-checkbox').checked) {
            // 今日不再显示第二天再显示
            var today = new Date();
            var endOfDay = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59);
            var expires = endOfDay.toUTCString();
            document.cookie = "dont_show_today=true; expires=" + expires + "; path=/";
        }
        document.getElementById('custom-announcement-popup').style.display = 'none';
    }
</script>

            <style>
    .custom-announcement-popup {
        display: block; 
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        max-width: 600px;
        background-color: #fff;
        padding: 20px;
        border: 2px solid <?php echo esc_attr($popup_border_color); ?>;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

                .custom-announcement-popup-content {
                    text-align: center;
                }

                .custom-announcement-popup-text {
                    font-size: 16px;
                    line-height: 1.6;
                }

                .custom-announcement-popup-close {
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin-top: 10px;
                    cursor: pointer;
                    border-radius: 5px;
                }

                .custom-announcement-popup-close:hover {
                    background-color: #45a049;
                }
            </style>
            <?php
        }
    }
}

function check_dont_show_today_cookie() {
    return isset($_COOKIE['dont_show_today']) && $_COOKIE['dont_show_today'] === 'true';
}

add_action('wp_footer', 'display_custom_announcement');

?>
