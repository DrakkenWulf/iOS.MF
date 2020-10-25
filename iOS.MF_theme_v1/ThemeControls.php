<?php

/*
* Template controls specific to the theme
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


//Add a quick login control where this method is called
function template_control_quick_login() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  //Button to show or hide the login form
  echo '
    <div id="show-hide-login" class="show-hide-login ', $context['user']['is_logged'] ? 'logout-icon' : 'login-icon', '"></div>';

  //Script to logout
  if ($context['user']['is_logged']) {
    echo '
    <script type="text/javascript">
      var control = $(".show-hide-login").last().get(0);
      control.onclick = function() { $(".ui-loader").loader("show"); window.location.href = "index.php?action=logout;sesc=', $context['session_id'], '"; };
    </script>';
  }
  //Script called by the button to show or hide the login form
  else {
    echo '
    <script type="text/javascript">
      var control = $(".show-hide-login").last().get(0);
      var toggleQuickLogin = function() {
        if ($("#quick-login").is(":visible"))
        {
          $(".user").first().blur();
          $("#quick-login").hide();
          control.className = "login-icon";
        }
        else
        {
          $("#quick-login").show();
          $(".user").first().focus();
          control.className = "close-icon";
        }
        setTopMargin();
      };
      control.onclick = toggleQuickLogin;
    </script>';
    
    //The login form
    echo '
    <div id="quick-login" class="quick-login">';

    template_control_login_form();

    echo '
      </div>';
  }
}

//The login form used throughout the theme
function template_control_login_form() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  echo '
  <form class="login" action="', $context['login_url'], '" name="frmLogin" id="frmLogin" method="post" accept-charset="', $context['character_set'], '">
      <div class="no-left-padding input-container pad-top">';
  echo '<span class="input-label">' . $txt['username'] . '</span>';
  echo '<input type="text" tabindex="', $context['tabindex']++, '" id="', !empty($context['from_ajax']) ? 'ajax_' : '', 'loginuser" name="user" value="', $context['default_username'], '" required>
  </div>
      <div class="no-left-padding input-container pad-top">';
  echo '<span class="input-label">' . $txt['password'] . '</span>';
  echo '<input type="password" tabindex="', $context['tabindex']++, '" id="', !empty($context['from_ajax']) ? 'ajax_' : '', 'loginpass" name="passwrd" value="', $context['default_password'], '" size="20" required>
  </div>
      <div class="no-left-padding input-container pad-top">';
  echo '<dt>', $txt['time_logged_in'], ':</dt>
        <dd>
          <select name="cookielength" id="cookielength">';

      foreach ($context['login_cookie_times'] as $cookie_time => $cookie_txt)
      echo '
            <option value="', $cookie_time, '"', $modSettings['cookieTime'] == $cookie_time ? ' selected' : '', '>', $txt[$cookie_txt], '</option>';

      echo '
          </select>
        </dd>';
	// If they have deleted their account, give them a chance to change their mind.
	if (isset($context['login_show_undelete']))
		echo '
						<dt class="alert">', $txt['undelete_account'], ':</dt>
						<dd><input type="checkbox" name="undelete"></dd>';
  	echo '
      </div>
      <div class="buttons" style="margin-top: -9px; padding-bottom: 5px;">
        <button onclick="$(\'.ui-loader\').loader(\'show\');" class="button two-buttons" type="submit">' . $txt['login'] . '</button>
        <button class="button two-buttons" type="button" onclick="go(\'register\')">' . $txt['register'] . '</button>
      </div>
      <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
      <input type="hidden" name="', $context['login_token_var'], '" value="', $context['login_token'], '">
      <script>
      setTimeout(function() {
        document.getElementById("', !empty($context['from_ajax']) ? 'ajax_' : '', isset($context['default_username']) && $context['default_username'] != '' ? 'loginpass' : 'loginuser', '").focus();
      }, 150);';

if (!empty($context['from_ajax']))
echo '
      form = $("#frmLogin");
      form.submit(function(e) {
        e.preventDefault();
        e.stopPropagation();

        $.ajax({
          url: form.prop("action"),
          method: "POST",
          data: form.serialize(),
          success: function(data) {
            if (data.indexOf("<bo" + "dy") > -1) {
              document.open();
              document.write(data);
              document.close();
            }
            else
              form.parent().html($(data).find(".roundframe").html());
          },
          error: function(xhr) {
            var data = xhr.responseText;
            if (data.indexOf("<bo" + "dy") > -1) {
              document.open();
              document.write(data);
              document.close();
            }
            else
              form.parent().html($(data).filter("#fatal_error").html());
          }
        });

        return false;
      });';

echo '
    </script>
  </form>';
}

//Add a quick search control where this method is called
function template_control_quick_search() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  //Button to show or hide the login form
  echo '
    <div id="show-hide-search" class="show-hide-search magnifier-icon" onclick="toggleSearch"></div>';

  //Funtion called by the button to show or hide the search form
  echo '
    <script type="text/javascript">
      var searchControl = $(".show-hide-search").last().get(0);
      var toggleSearch = function() {
        if ($("#search-bar").is(":visible"))
        {
          $("#search-bar").hide();
          searchControl.className = "magnifier-icon";
        }
        else
        {
          $("#search-bar").show();
          searchControl.className = "close-icon";
          $("#search-text").focus();
        }
        setTopMargin();
      };
      searchControl.onclick = toggleSearch;
    </script>';

  //The search form
  echo '
    <div id="search-bar" class="input-container">
      <form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="search-form">
        <input id="search-text" type="text" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', !empty($context['search_string_limit']) ? ' maxlength="' . $context['search_string_limit'] . '"' : '', ' tabindex="', $context['tabindex']++, '" />
        <input type="submit" class="button input-button" value="' . $txt['iSearch'] . '" onclick="if(document.searchform.search.value.length<3){alert(\'', $txt['iSearchAlert'], '\');return false;}" />
      </form>
    </div>';
}

//Add quick reply functionality to the topbar
function template_control_quick_reply() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  //Autosize the editor
  $quickReply = '
    <script type="text/javascript">
      $(function(){
        $(".editor").last().autosize();
      });';

  //Function to show or hide the quick reply form
  $quickReply .= '
      var toggleQuickReply = function() {
        if ($("#quick-reply").is(":visible"))
        {
          $("#message").blur();
          $("#quick-reply").hide();      
        }
        else
        {
          $("#quick-reply").show();
          $("#message").focus();
        }
        setTopMargin();
      };';

  //Add the call to toggleQuickReply to the title
  $quickReply .= '
      var title = $(".the-title").last().get(0);
      title.onclick = function() { $(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0); toggleQuickReply();};
      $(".the-title").addClass("quick-reply-title");';

  //Function to call when submitting the post
  $quickReply .= '
      var submitForm = function() {
        submitonce(this);
        smc_saveEntities("postmodify", ["subject", "' . (isset($context['post_box_name']) ? $context['post_box_name'] : 'message') . '", "guestname", "evtitle", "question"], "options");
      };
    </script>';

  //The quick reply form  
  $quickReply.= '
    <div id="quick-reply">
      <form data-ajax="false" action="' . $scripturl . '?action=post2;' . (empty($context['current_board']) ? '' : 'board=') . $context['current_board'] . '.new#new" method="post" accept-charset="' . $context['character_set'] . '" name="postmodify" id="postmodify" onsubmit="submitForm();" enctype="multipart/form-data" style="margin: 0;">
        <div id="post-container" class="input-container" style="padding-bottom: 0;">
          <div class="new-post">
            <textarea class="editor" name="message" id="message" rows="1" cols="60" tabindex="2" style="width: 100%; height: 16px; overflow: hidden; word-wrap: break-word; resize: horizontal;"></textarea>
          </div>
        </div>';
  
  //Guests have to put in their name and email
  if (!$context['user']['is_logged'] && isset($context['name']) && isset($context['email'])) {
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">' . $txt['username'] . '</span>';
    $quickReply.= '<input type="text" name="guestname" size="25" value="' . $context['name'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
    $quickReply.= '<span id="smf_autov_username_div" style="display: none;">
                     <a id="smf_autov_username_link" href="#">
                       <img id="smf_autov_username_img" src="' . $settings['images_url'] . '/icons/field_check.png" alt="*" />
                     </a>
                   </span>';
    $quickReply.= '</div>';
    
    if (empty($modSettings['guest_post_no_email'])) {
      $quickReply.= '<div class="no-left-padding input-container pad-top">';
      $quickReply.= '<span class="input-label">' . $txt['email'] . '</span>';
      $quickReply.= '<input type="text" name="email" size="25" value="' . $context['email'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
      $quickReply.= '</div>';
    }
  }
  
  //Verification control
  if ($context['require_verification']) {
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">' . $txt['iCode'] . '</span>';
    $quickReply.= template_control_verification($context['visual_verification_id'], 'all');
    $quickReply.= '</div>';
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">' . $txt['iVerify'] . '</span>';
    $quickReply.= '<input type="text" tabindex="' . $context['tabindex']++ . '" name="post_vv[code]" />';
    $quickReply.= '</div>';
  }
  
  //Submit button
  $quickReply.= '
        <div class="child buttons">
          <button class="button" type="submit" onclick="$(\'.ui-loader\').loader(\'show\');">' . $txt['iPost'] . '</button>
        </div>';

  //Inputs  
  if (isset($context['num_replies'])) {
    $quickReply.= '
        <input type="hidden" name="num_replies" value="' . $context['num_replies'] . '" />';
  }
  if (!empty($context['subject'])) {
    $quickReply.= '
       <input type="hidden" name="subject" value="' . $context['subject'] . '" />';
  }
  $quickReply.= '
        <input type="hidden" name="attach_del[]" value="0" />
        <input type="hidden" name="additional_options" value="' . (isset($context['show_additional_options']) && $context['show_additional_options'] ? 1 : 0) . '" />
        <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
        <input type="hidden" name="seqnum" value="' . $context['form_sequence_number'] . '" />
        <input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
        <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />    
        <input type="hidden" name="goback" value="' . $options['return_to_post'] . '" />
        <input type="hidden" name="icon" value="">
      </form>';
  
  $quickReply.= '
    </div>';
  
  //Finally add the code to the topbar
  echo '<script type="text/javascript">
    $(function() {
      $(".topbar").last().append(', json_encode($quickReply), ');
    });
  </script>';
}

//Generate a paging control
function template_control_paging($pageIndex = null) {
  global $context, $txt;
  echo $context['page_index'];
}

//Generate a paging control
function template_control_pagingXXX($pageIndex = null) {
  global $context, $txt;
  
  //If we've passed in a page index we probably don't have the usual context information available to us. We will have to work out some information from it
  if ($pageIndex) {
    parse_page_control($pageIndex);
  }
  
  //Add tap and hold functions to the relevant buttons
  echo '
    <script type="text/javascript">
      $(function(){
        Hammer($(".previous-page").last()).on("tap", function(event) {
          $.mobile.changePage(\'', $context['links']['prev'], '\');
        });
        Hammer($(".next-page").last()).on("tap", function(event) {
          $.mobile.changePage(\'', $context['links']['next'], '\');
        });
        Hammer($(".previous-page").last()).on("hold", function(event) {
          $.mobile.changePage(\'', $context['links']['first'], '\');
        });
        Hammer($(".next-page").last()).on("hold", function(event) {
          $.mobile.changePage(\'', $context['links']['last'], '\');
        });
      });
    </script>';

//    <button id="page-count">', $txt['iPage'], ' ', $context['page_info']['current_page'], ' ', $txt['iOf'], ' ', ($context['page_info']['num_pages'] == 0) ? '1' : $context['page_info']['num_pages'], '</button>

//Output our paging control
  echo '
    <div id="page-buttons" class="page buttons">
    "',$pageIndex,'"
      <button id="previous-page" class="previous-page button" ', $context['page_info']['current_page'] == 1 ? 'disabled="disabled"' : '', '>', $txt['iPrev'], '</button>
      <button id="page-count">', $txt['iPage'], ' ', $context['page_info']['current_page'], ' ', $txt['iOf'], ' ', ($context['page_info']['num_pages'] == 0) ? '1' : $context['page_info']['num_pages'], '</button>
      <button id="next-page" class="next-page button" ', ($context['page_info']['current_page'] == $context['page_info']['num_pages'] || $context['page_info']['num_pages'] == 0) ? 'disabled="disabled"' : '', '>', $txt['iNext'], '</button>
    </div>';
}

//Backward engineer paging links and details from a page index contol
function parse_page_control($pageIndex) {
  global $context;
  
  //Find the current page number
  $currentPageStart = strrpos($pageIndex, '<strong>') + 8;
  $currentPageEnd = strrpos($pageIndex, '</strong>');
  $currentPage = substr($pageIndex, $currentPageStart, $currentPageEnd - $currentPageStart);
  
  //Find the number of the last page
  $lastLinkEnd = strrpos($pageIndex, '</a>');
  $lastLinkStart = $lastLinkEnd ? strrpos($pageIndex, '>', -strlen($pageIndex) + $lastLinkEnd) + 1 : false;
  $lastLinkedPage = $lastLinkStart && $lastLinkEnd ? substr($pageIndex, $lastLinkStart, $lastLinkEnd - $lastLinkStart) : 1;
  
  //Find the count on the last page (the total number of posts)
  $lastPageCountEnd = $lastLinkStart ? $lastLinkStart - 2 : false;
  $lastPageCountStart = $lastPageCountEnd ? strrpos($pageIndex, 'start=', -strlen($pageIndex) + $lastPageCountEnd) + 6 : false;
  $lastPageCount = $lastPageCountStart && $lastPageCountEnd ? substr($pageIndex, $lastPageCountStart, $lastPageCountEnd - $lastPageCountStart) : 1;

  //How many posts per page?
  $pageSize = $lastLinkedPage > 1 ? $lastPageCount / ($lastLinkedPage - 1) : 1;
  
  //What are the numbers of the first and last pages?
  $firstPage = 1;
  $lastPage = $currentPage > $lastLinkedPage ? $currentPage : $lastLinkedPage;
  
  //What are the numbers of the previous and next pages?
  $previousPage = $currentPage >= 2 ? $currentPage - 1 : 1;
  $nextPage = $currentPage == $lastPage ? $lastPage : $currentPage + 1;
  
  //Get the url which we can use to navigate to the relevant pages
  $urlStart = strrpos($pageIndex, 'href="') + 6;
  $urlEnd = $urlStart ? strrpos($pageIndex, '"', $urlStart) : false;
  $url = $urlStart && $urlEnd ? substr($pageIndex, $urlStart, $urlEnd - $urlStart) : '';
  $url = substr($url, 0, strrpos($url, ';')) . ';start=';
  
  //Store the links and page information in the context where we would usually expect to find it
  $context['links'] = array(
    'first' => $url . (($firstPage - 1) * $pageSize),
    'prev' => $url . (($previousPage - 1) * $pageSize),
    'next' => $url . (($nextPage - 1) * $pageSize),
    'last' => $url . (($lastPage - 1) * $pageSize),);
  $context['page_info'] = array('current_page' => $currentPage, 'num_pages' => $lastPage,);
}

?>