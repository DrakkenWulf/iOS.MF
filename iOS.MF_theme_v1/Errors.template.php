<?php

/*
* This is the error view which we will see when something unexpected happens
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


function template_fatal_error() {
  global $context;
  
	echo '<div class="errors"><div style="margin-top: 6px;">*', $context['error_message'] , '</div></div>';
}

function template_error_log() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_show_file() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

?>