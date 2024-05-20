<?php
/**
 * pr0gramm-antiAnanas
 * 
 * Anti-Ananas.Club Autoresponse Bot for the German imageboard pr0gramm.com
 * 
 * @author    RundesBalli <rundesballi@rundesballi.com>
 * @copyright 2024 RundesBalli
 * @version   1.0
 * @license   MIT-License
 */

/**
 * Include the configuration file.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'config.php');

/**
 * Include the apiCall.
 */
require_once($apiCall);

/**
 * Loop everything, because it runs as a systemd service.
 */
while(1) {
  /**
   * Log microtime
   */
  $microtimeStart = microtime(true);

  /**
   * Check if there are any comments in the inbox.
   * 
   * To save a big amount of traffic and load at pr0gramm, only the sync will be called to get the number of
   * elements in the inbox.
   */
  $inbox = apiCall("https://pr0gramm.com/api/user/sync?offset=99999999")['inbox'];
  $inboxCount = $inbox['comments']+$inbox['mentions'];
  if(!$inboxCount) {
    if($debug) {
      echo 'Nothing to do...'."\n";
    }
    sleep($timeout);
    continue;
  }

  /**
   * At least one element is available at the inbox. Now the api for the whole inbox is called and reversed,
   * so the oldest element is at first.
   */
  $response = apiCall("https://pr0gramm.com/api/inbox/all")['messages'];
  $response = array_reverse($response);

  /**
   * Iterate through the response.
   */
  foreach($response as $message) {
    if($message['read'] == 0 AND $message['type'] == 'comment' AND preg_match('/@'.$pr0Username.'/i', $message['message'], $matches) === 1) {
      /**
       * Only unread mentions of the username entered in the pr0gramm-apiCall configuration will be answered.
       * This prevents, that the same procedure of this bot will be executed again when someone has replied
       * to the bot's comment. Furthermore it will be checked, if the bot has been posting a comment before in
       * this comment tree.
       */


      /**
       * Get the whole tree.
       */
      $treeResponse = apiCall("https://pr0gramm.com/api/items/info?itemId=".$message['itemId'])['comments'];
      $comments = [];
      foreach($treeResponse as $val) {
        $comments[$val['id']] = $val;
      }
      $usernames = [];
      $parent = $comments[$message['id']]['id'];
      do {
        $usernames[] = $comments[$parent]['name'];
        $parent = $comments[$parent]['parent'];
      } while($parent != 0);
      $usernames = array_unique($usernames);
      $key = array_search('Gamb', $usernames);
      if($key !== FALSE) {
        unset($usernames[$key]);
      }

      /**
       * Check if the bot has been triggered before in this comment tree.
       */
      if(array_search($pr0Username, $usernames)) {
        continue;
      }

      /**
       * Prepare the text.
       */
      $text = 'Der Anti-Ananas-Bot verkündet folgende Weisheit:'."\n".$phrases[array_rand($phrases)];
      if(!empty($usernames)) {
        $text.= "\n\n".'@'.implode(', @', $usernames);
      }

      /**
       * Post the text.
       */
      $response = apiCall("https://pr0gramm.com/api/comments/post", ['itemId' => $message['itemId'], 'parentId' => $message['id'], '_nonce' => $nonce, 'comment' => $text]);

      /**
       * Check if the max level has been reached.
       */
      if(!empty($response['error']) AND $response['error'] == 'maxLevels') {
        $text = 'Der Anti-Ananas-Bot verkündet für den Kommentarbaum https://pr0gramm.com/new/'.$message['itemId'].':comment'.$message['id'].' (letzte Kommentarebene erreicht) folgende Weisheit:'."\n".$phrases[array_rand($phrases)];
        if(!empty($usernames)) {
          $text.= "\n\n".'@'.implode(', @', $usernames);
        }
        $response = apiCall("https://pr0gramm.com/api/comments/post", ['itemId' => $message['itemId'], 'parentId' => 0, '_nonce' => $nonce, 'comment' => $text]);
      }
    }
  }

  /**
   * Output execution time
   */
  $executionTime = microtime(true)-$microtimeStart;
  if($debug) {
    echo 'Execution time: '.round($executionTime*1000, 1).' ms'."\n";
    echo 'Sleeping for '.$timeout.' second'.($timeout != 1 ? 's' : NULL);
    echo "\n-------------------------------------------------------------\n";
  }
  sleep($timeout);
  continue;
}
?>
