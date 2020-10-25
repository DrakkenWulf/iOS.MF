<?php

/*
* The index template, used to generate headers, footers, toolbars and general content that will appear on every theme page
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


require_once ($settings['theme_dir'] . '/ThemeControls.php');
require_once ($settings['theme_dir'] . '/ThemeFunctions.php');

function template_init() {
  global $context, $settings, $options, $txt;
  
  $settings['theme_version'] = '1.1';
  $settings['require_theme_strings'] = true;
  
  //Disable SimplePortal
  $settings['disable_sp'] = true;
  
  //Disable TinyPortal
  if (function_exists('tp_hidebars')) {
    tp_hidebars();
  }
  
  //Disable PortaMX
  $_SESSION['pmx_paneloff'] = array('head', 'top', 'left', 'right', 'bottom', 'foot', 'front', 'pages' => 'Pages');
}

function template_html_above() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  echo '
    <!DOCTYPE html>
    <html', $context['right_to_left'] ? ' dir="rtl"' : '', '>

    <head>
      <title>', $context['page_title_html_safe'], '</title>

      <meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
      <meta name="description" content="', $context['page_title_html_safe'], '" />
      ', !empty($context['meta_keywords']) ? '<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />

      <link type="text/css" rel="stylesheet" media="screen" href="', $settings['theme_url'], '/css/index.css" />
      <link type="text/css" rel="stylesheet" href="' . $settings['theme_url'] . '/css/jquery.mobile.structure-1.4.2.min.css" />
      <link type="text/css" rel="stylesheet" href="' . $settings['theme_url'] . '/css/magnific-popup.css" />
      ', ($context['right_to_left'] ? '<link type="text/css" rel="stylesheet" href="' . $settings['default_theme_url'] . '/css/rtl.css" />' : ''), '

      <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/fastclick.min.js"></script>
      <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery-2.1.1.min.js"></script>
      <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.mobile-1.4.2.min.js"></script>
      <script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/script.js?fin20"></script>
      <script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/captcha.js"></script>
      <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.autosize.min.js"></script>
      <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.hammer.min.js"></script>
      <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/theme.js"></script>
      <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/jquery.magnific-popup.min.js"></script>

      <script type="text/javascript">
        var disableQuoting = ', (isset($_COOKIE['disablequoting'])) ? 'true' : 'false', ';
        var quotingOff = "', $txt['iQuoting'], ' ', $txt['iOff'], '";
        var loading = "', $txt['iLoading'], '...";
        var smf_theme_url = "', $settings['theme_url'], '";
        var smf_default_theme_url = "', $settings['default_theme_url'], '";
        var smf_images_url = "', $settings['images_url'], '";
        var smf_scripturl = "', $scripturl, '";
        var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
        var smf_charset = "', $context['character_set'], '";
        var ajax_notification_text = "', $txt['ajax_in_progress'], '";
        var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";';

  //Set the jQuery Mobile page transition
  echo '
        $.mobile.defaultPageTransition = \'', isset($settings['page_transition_animation']) ? $settings['page_transition_animation'] : 'none', '\';';

  //Initialise FastClick, this makes it nice and responsive on mobiles
  echo '
        $(function() {
          FastClick.attach(document.body);
        });';

  //Set the size of the margin at the top of the page which is dependant on whether the quick search, quick login or quick reply is showing
  echo '
        var setTopMargin = function() {
          var topBar = $(".topbar").last();
          if (topBar.css("position") == "fixed") {
            var topBarHeight = topBar.height();
            $(".margin-top-content").last().css("padding-top", topBarHeight);
          }
        }

        $(document).on("pagecontainershow", function() {
          setTopMargin();
        });';

  //Check whether we need to reload the page because we're not actually in the theme anymore but loaded up another theme via jQuery mobile
  echo '
        $(document).on("pagecontainershow", function() {
          if (!$(".ios-mf-page").length) {
            location.reload();
          }
        });

      </script>';
  
  //If selected then prevent the default press and hold behaviour in Safari/Chrome, this stops items being selected when using press and hold buttons 
  if (isset($settings['disable_webkit_select']) && $settings['disable_webkit_select']) {
    echo '
      <style type="text/css">
        body {
          -webkit-user-select: none;
        }
      </style>';
  }
  
  //If selected then make the toolbar transparent
  if (isset($settings['enable_transparent_toolbar']) && $settings['enable_transparent_toolbar']) {
    echo '
      <style type="text/css">
        .toolbar {
          background: rgba(240,240,244,0.75) !important;
        }
      </style>';
  }
        
  echo '
    </head>';
}

function template_body_above() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  //ToDo: Add fixed-top-bar class to topbar to fix at the top. Safari doesn't like fixed items when the keyboard is showing at present though (iOS7).
  echo '
    <body>
      <div id="wrapper" data-role="page" class="ios-mf-page">
        <div data-enhance="false">
          <div class="topbar" id="topbar" data-role="header">

            <div id="page-title">
              <div id="the-title" class="the-title">', parse_title(), '</div>
            </div>';
  
  template_control_quick_login();
  template_control_quick_search();
  
  echo '
          </div>';
  
  //Wrapper for the main content in the page
  echo '
          <div class="margin-top-content">';
}

function template_body_below() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  //Default mode button
  $themeNumber = '1';
  if (!empty($modSettings['id_default_theme'])) {
    $themeNumber = $modSettings['id_default_theme'];
  } else {
    $themeNumber = $modSettings['theme_guests'];
  }
  $currentUrl = get_current_url();
  $backLink = "index.php?theme=" . $themeNumber . ";";
  if (strpos($currentUrl, 'index.php') !== false) {
    $backLink = $currentUrl;
    $backLink.= strpos($backLink, '?') == false ? '?' : (substr($backLink, -1) !== ';' ? ';' : '');
    $backLink.= strpos($backLink, 'theme=') == false ? 'theme=' . $themeNumber . ';' : '';
    $backLink = preg_replace("/theme=\d+/", "theme=" . $themeNumber, $backLink);
  }
  $backName = $txt['iDefaultTheme'];
  echo '
        <button data-ajax="false" class="classic button" id="classic">', $backName, '</button>';
  
  //Tap the default button to go to the forum default theme, hold the default button to go to the SMF default theme
  echo '
        <script type="text/javascript">
          Hammer($(".classic").last()).on("tap", function(event) {
            $(".ui-loader").loader("show");
            window.location.href = "', $backLink, '";
          });

          Hammer($(".classic").last()).on("hold", function(event) {
            $(".ui-loader").loader("show");
            window.location.href = "', preg_replace("/theme=\d+/", "theme=1", $backLink), '";
          });
        </script>';
  
  //Copyright
  echo '
        <div id="copyright">
          <h4>', theme_copyright(), '</h4>
        </div>';
  
  //Fixed toolbar at the bottom of the page

  //Have we been asked not to show either of the unread counts?
  $disable_personal_message_count = (isset($settings['disable_personal_message_count']) && $settings['disable_personal_message_count']);
  $disable_unread_topic_count = (isset($settings['disable_unread_topic_count']) && $settings['disable_unread_topic_count']);

  //Find the number of unread topics
  $unreadTopicCount = $disable_unread_topic_count || !function_exists('iosmf_unread_topic_count') ? 0 : iosmf_unread_topic_count();
  
  //Use javascript to set post count as the toolbar isn't reloaded each time; we need to do this within main page content
  echo '
        <script type="text/javascript">
          $(document).one("pageload", function() {
            var unreadTopicCount = ', $unreadTopicCount, ';
            var unreadMessageCount = ', $context['user']['unread_messages'], ';

            var unreadTopicCountElement = $(".unread-posts").last();
            var unreadMessageCountElement = $(".unread-messages").last();

            unreadTopicCountElement.get()[0].innerHTML = unreadTopicCount;
            unreadMessageCountElement.get()[0].innerHTML = unreadMessageCount;

            if (unreadTopicCount != 0) {
              unreadTopicCountElement.show();
            }
            else {
              unreadTopicCountElement.hide();
            }

            if (unreadMessageCount != 0) {
              unreadMessageCountElement.show();
            }
            else {
              unreadMessageCountElement.hide();
            }
          });
        </script>';
  
  echo '
      </div>
    </div>';

  echo '
    <div id="toolbar" class="toolbar" data-role="footer" data-id="footer" data-position="fixed" data-tap-toggle="false" data-enhance="true">
      <div>
        <div class="toolbar-icon" onclick="$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'home\');" style="background: url(' . $settings['theme_url'] . '/images/icons/home.png) transparent center no-repeat;"></div>
      </div>
      <div>
        <div class="toolbar-icon" onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'profile\');' : '', '" style="background: url(' . $settings['theme_url'] . '/images/icons/person.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;', '"></div>
      </div>
      <div>
        <div class="toolbar-icon" onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'pm\');' : '', '" style="background: url(' . $settings['theme_url'] . '/images/icons/messages.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;', '"></div>
        <div class="unread-count unread-messages"', ($context['user']['unread_messages'] > 0 && $context['user']['is_logged'] && !$disable_personal_message_count ? '>' . $context['user']['unread_messages'] : ' style="display:none;">'), '</div>
      </div>
      <div>
        <div class="toolbar-icon" onclick="$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'recent\');" style="background: url(' . $settings['theme_url'] . '/images/icons/inbox.png) transparent center no-repeat;"></div>
      </div>
      <div>
        <div class="toolbar-icon" onclick="', $context['user']['is_logged'] ? '$(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0);go(\'unread;all\');' : '', '" style="background: url(' . $settings['theme_url'] . '/images/icons/newpost.png) transparent center no-repeat; ', $context['user']['is_logged'] ? '' : ' opacity: 0.3;', '"></div>
        <div class="unread-count unread-posts"', ($unreadTopicCount > 0 && $context['user']['is_logged'] && !$disable_unread_topic_count ? '>' . $unreadTopicCount : ' style="display:none">'), '</div>
      </div>
    </div>';
}

function template_html_below() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  echo '
        </div>
      </body>
    </html>';
}

function template_button_strip() {
}

function theme_linktree() {
}

function template_menu() {
}

?>