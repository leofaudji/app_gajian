<?php defined('BASEPATH') OR exit('CV. ILMION KREATIF - ILMION STUDIO - https://ilmion.com بسم الله الرحمن الرحيم ');
/**    
 * @copyright   ALKANTARA
 * @link        https://ilmion.com
 * @author      Faudji
 * @version     0.0.1
 * @since       11 Jul 25
 */

class Telegram extends Bismillah_Model{

  define('BOT_TOKEN', '7674476004:AAHLb-BiTP9iEoR2XpRW3YLWVKKuip9Mo2U');

  // URL API Telegram
  define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

  public function apiRequest($method, $params = []) {
      // Bangun URL lengkap untuk permintaan API
      $url = API_URL . $method;   

      // Inisialisasi cURL
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mengembalikan respons sebagai string
      curl_setopt($ch, CURLOPT_POST, true); // Menggunakan metode POST
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); // Mengirim parameter sebagai POST fields

      // Eksekusi permintaan cURL
      $response = curl_exec($ch);

      // Tangani kesalahan cURL
      if (curl_errno($ch)) {
          error_log("cURL Error: " . curl_error($ch));
          return false;
      }

      // Tutup sesi cURL
      curl_close($ch);

      // Dekode respons JSON
      return json_decode($response, true);
  }

  
  public function sendMessage($chat_id, $text, $parse_mode = '') {
      $params = [
          'chat_id' => $chat_id,
          'text' => $text,
      ];
      if (!empty($parse_mode)) {
          $params['parse_mode'] = $parse_mode;
      }
      return apiRequest('sendMessage', $params);
  }

  

}