<?php
    class Languagetranslator
    {
        const ENDPOINT = 'https://www.googleapis.com/language/translate/v2';
        protected $_apiKey;

        public function __construct()
        {
           $this->_apiKey = "AIzaSyDCiUIQgd4un6EUDgD3o5p_BUyLI0kvTmw";
        }

        public function translate($data, $target, $source = '')
        {
            $values = array(
                'key'    => $this->_apiKey,
                'target' => $target,
                'q'      => $data
            );

            if (strlen($source) > 0) {
                $values['source'] = $source;
            }

            $formData = http_build_query($values);

            $ch = curl_init(self::ENDPOINT);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET'));

            $json = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($json, true);

            if (!is_array($data) || !array_key_exists('data', $data)) {
                throw new Exception('Unable to find data key');
            }
            if (!array_key_exists('translations', $data['data'])) {
                throw new Exception('Unable to find translations key');
            }

            if (!is_array($data['data']['translations'])) {
                throw new Exception('Expected array for translations');
            }
            foreach ($data['data']['translations'] as $translation) {
                return $translation['translatedText'];
            }
            throw new Exception('Translation failed');
        }
    }
?>
