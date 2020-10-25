<?php

/*
* View all the different forum boards grouped by category (this is the top level home view)
*
* License: http://www.opensource.org/licenses/mit-license.php
*/

/**
 * The top part of the outer layer of the boardindex
 */
function template_boardindex_outer_above()
{
}


function template_main() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  //Loop through all the categories
  $firstContent = true;
  foreach ($context['categories'] as $category) {
    echo '
      <ul class="content-list', $firstContent ? ' first-content">' : '">';
    $firstContent = false;

    //Loop through each categorie's boards and output a list item for each board
    foreach ($category['boards'] as $board) {
      echo '
        <li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $board['href'] . '\');">';
      echo '
          <div class="title', ($context['user']['is_logged'] && ($board['new'] || $board['children_new'])) ? ' short-title' : '', '">', $board['name'], '</div>';
      if ($context['user']['is_logged'] && ($board['new'] || $board['children_new'])) {
        echo '
          <div class="new">' . $txt['iNew'] . '</div>';
      }
      echo '
          <div class="description">', $board['last_post']['member']['name'], ', ', $board['last_post']['time'] == 'N/A' ? $txt['no'] . ' ' . $txt['topics'] : parse_time($board['last_post']['timestamp']), '</div>';
      echo '
        </li>';
    }
    
    echo '
      </ul>';
  }
  
  //List the online users in order of activity.
  if (!empty($context['users_online'])) {
    echo '
      <div class="content-list">';
    echo '
        <span class="mieilink">', implode(', ', $context['list_users_online']) , '</span>';
    echo '
      </div>';
  }
}

/**
 * The lower part of the outer layer of the board index
 */
function template_boardindex_outer_below()
{
	#template_info_center();
}

?>