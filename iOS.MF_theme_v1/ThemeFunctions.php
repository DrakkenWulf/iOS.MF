<?php

/*
* Functions for use within the theme.
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


//Make some adjustments to the forum title
function parse_title() {
  global $context, $txt;
  
  $title = str_replace($context['forum_name_html_safe'] . ' - ', '', $context['page_title_html_safe']);
  
  if ($title == $txt['iIndex']) {
    $title = $context['forum_name_html_safe'];
  }
  
  $title = str_replace($txt['profile_of'] . ' ', '', $title);
  $title = str_replace($txt['iSearchParameters'], $txt['search'], $title);
  $title = str_replace($txt['iPersonalMessagesIndex'], $txt['personal_messages'], $title);
  $title = str_replace($txt['iSendMessage'], $txt['iComposeMessage'], $title);
  
  return $title;
}

//Script to navigate to a msg or new element if it is specified in the URL
function script_navigate_to_message() {
  echo '
  <script type="text/javascript">

    //silentscroll is only called when the page is loaded, so we will always want to navigate the element in question
    $(document).one("silentscroll", function() {
      if (!navigateToElement(/(msg\d+)/))
      {
        navigateToElement(/(new)/);
      }
    });

    //pagecontainertransition is called when moving forward and back through the history, so we only want to do this navigation once
    $(document).one("pagecontainertransition", function() {
      if (!navigateToElementOnce(/(msg\d+)/))
      {
        navigateToElementOnce(/(new)/);
      }
    });

    //Navigate to an element if we can find it
    var navigateToElement = function(regex) {
      var elementMatch = location.search.substring(1).match(regex);
      if (elementMatch)
      {
        var elementId = elementMatch[0];
        if (elementId && $("#"+ elementId).length)
        {
          $("#"+ elementId)[0].scrollIntoView(true);
          return true;
        }
      }
      return false;
    };

    //Navigate to an element if we can find it, but only once (not when moving forward and back through the history)
    var navigateToElementOnce = function(regex) {
      var elementMatch = location.search.substring(1).match(regex);
      if (elementMatch)
      {
        var elementId = elementMatch[0];
        var state = window.history.state;
        if (elementId && $("#"+ elementId).length && (!state.hasOwnProperty("preventNavigationToPost")))
        {
          $("#"+ elementId)[0].scrollIntoView(true);
          state.preventNavigationToPost = true;
          history.replaceState(state, "", document.URL);
          return true;
        }
      }
      return false;
    };

  </script>';
}

//Hide the toolbar when the keyboard is shown on an iOS or Android device
function script_hide_toolbar() {
  global $context;
  
  echo '<script type="text/javascript">
      $(function(){

        //Deal with the race condition between iOS keyboard showing and the focus event firing
        if(/iPhone|iPod|Android|iPad/.test(window.navigator.platform)){
          var jqElement = $(".editor").last();
          jqElement.attr("disabled", true);

          jqElement.on("tap", function(event) {
            if (event.target.id == "', (isset($context['post_box_name']) ? $context['post_box_name'] : 'message'), '") {
              if (!$(event.target).is(":focus")) {

                // Hide toolbar
                $(".toolbar").css("display", "none");
                $("#copyright").css("margin-bottom", "4px");

                //Enable and focus textbox
                $(event.target).removeAttr("disabled");
                $(event.target).focus();

                //Move caret to end
                jqElement.get(0).setSelectionRange(jqElement.val().length, jqElement.val().length);
              }
            }
          });

          jqElement.on("blur", function(e) {
            jqElement.attr("disabled", true);
          });
        }
      });

    </script>';  
}

//Get the current URL, this is surprisingly complex in PHP!
function get_current_url() {
  $url = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
  $url.= ($_SERVER["SERVER_PORT"] !== "80") ? ":" . $_SERVER["SERVER_PORT"] : "";
  $url.= $_SERVER["REQUEST_URI"];
  return $url;
}

//Take a time and turn it into the time elaspsed since
function parse_time($time) {
  global $txt;
  
  //The time since the input time in seconds
  $diff = forum_time() - $time;
  
  if ($diff < 60) return $diff . ' ' . $txt['iSecondsAgo'];
  elseif (round($diff / 60) == 1) return '1 ' . $txt['iMinuteAgo'];
  elseif ($diff > 59 && $diff < 3600) return round($diff / 60) . ' ' . $txt['iMinutesAgo'];
  elseif (round($diff / 60 / 60) == 1) return '1 ' . $txt['iHourAgo'];
  elseif (round($diff / 60 / 60) > 1 && round($diff / 60 / 60) < 24) return round($diff / 60 / 60) . ' ' . $txt['iHoursAgo'];
  elseif (round($diff / 60 / 60 / 24) == 1) return '1 ' . $txt['iDayAgo'];
  elseif (round($diff / 60 / 60 / 24) > 1 && round($diff / 60 / 60 / 24) < 7) return round($diff / 60 / 60 / 24) . ' ' . $txt['iDaysAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7) == 1) return '1 ' . $txt['iWeekAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7) > 1) return round($diff / 60 / 60 / 24 / 7) . ' ' . $txt['iWeeksAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7 / 4) == 1) return '1 ' . $txt['iMonthAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7 / 4) > 1) return round($diff / 60 / 60 / 24 / 7) . ' ' . $txt['iMonthsAgo'];
  else return $diff;
}

//Parse a message
function parse_message($message) {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  //Shorten any links in the message
  $message = ' ' . $message;
  $message = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='$2'>$2</a>", $message);
  $message = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='http://$2'>$2</a>", $message);
  $links = explode('<a', $message);
  $countlinks = count($links);
  for ($i = 0; $i < $countlinks; $i++) {
    $link = $links[$i];
    
    $link = (preg_match('#(.*)(href=")#is', $link)) ? '<a' . $link : $link;
    
    $begin = strpos($link, '>') + 1;
    $end = strpos($link, '<', $begin);
    $length = $end - $begin;
    $urlname = substr($link, $begin, $length);
    
    $chunked = (strlen(str_replace('http://', '', $urlname)) > 28 && preg_match('#^(http://|ftp://|www\.)#is', $urlname)) ? substr_replace(str_replace('http://', '', $urlname), '.....', 12, -12) : $urlname;
    $message = str_replace('>' . $urlname . '<', '>' . $chunked . '<', $message);
  }
  $message = preg_replace("#(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $message);
  $message = substr($message, 1);

  //Replace default smilies with retina smilies
  $message = str_replace(rtrim($scripturl, '/index.php') . '/Smileys/default/', $settings['theme_url'] . '/images/SkypeEmoticons/', $message);

  //Unbold "Today" text in quotes
  $message = str_replace('<strong>' . $txt['iToday'] . '</strong>', $txt['iToday'], $message);
  return ($message);
}

?>