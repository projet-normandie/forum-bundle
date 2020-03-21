<?php
namespace ProjetNormandie\ForumBundle\Filter;

class Bbcode
{
    protected $_config = array(
        'url_limit' => 200,
        'smiley_path' => "https://picture.video-games-records.com/smiley/images/",
        'smiley_ext' => ".png"
    );

    protected $_smileys = array(
        "hum", "ok", "cute", "damn", "no", "surprised", "happy", "slurp", "uhh", "hidden",
        "smile", "cool", "burnt", "robot", "think", "argh", "lol", "love", "whistle", "ko",
        "sick", "so", "yes", "wtf", "what", "cry", "sad", "pff", "angry", "dead",
        "aww", "well", "sleep"
    );


    //ACCESSEURS
    public function getSmiley()
    {
        return $this->_smileys;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function __construct($config = null)
    {
        if (!is_null($config) && is_array($config)) {
            foreach ($this->_config as $k => $v) {
                $this->_config[$k] = (isset($config[$k])) ? $config[$k] : $this->_config[$k];
            }
        }
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $text, when bbcode are replace by html code
     *
     * @param  string $text
     * @return string
     */
    public function filter($text)
    {
        //removing /r because it's bad!
        $text = str_replace("\r", '', $text);
        //transform all  double spaces in ' &nbsp;' to respect multiples spaces
        $text = str_replace('  ', ' &nbsp;', $text);

        // first [nobbcode][/nobbcode] -> don't interpret bbcode
        $this->_parseBbcodeNobbcode($text);

        // parse strange bbcode, before other bbcode
        $this->_parseBbcodeCode($text);
        $this->_parseBbcodeQuote($text);

        $this->_parseBbcodeList($text);

        // easy bbcode replacement

        // [i]txt[/i]
        $this->_parseSimpleBbcode('i', '<em>$1</em>', $text);
        // [u]txt[/u]
        $this->_parseSimpleBbcode('u', '<u>$1</u>', $text);
        // [b]txt[/b]
        $this->_parseSimpleBbcode('b', '<strong>$1</strong>', $text);
        // [del]txt[/del] & [strike]txt[/strike]
        $this->_parseSimpleBbcode('del', '<del>$1</del>', $text);
        $this->_parseSimpleBbcode('strike', '<del>$1</del>', $text);

        // [color=color]txt[/color]
        $this->_parseParamBbcode('color', '([a-zA-Z]*|\#?[0-9a-fA-F]{6})', '<span style="color: $1">$2</span>', $text);
        // [bgcolor=color]txt[/bgcolor]
        $this->_parseParamBbcode('bgcolor', '([a-zA-Z]*|\#?[0-9a-fA-F]{6})', '<span style="background-color: $1">$2</span>', $text);
        // [align=(center|left|right)][/align]
        $this->_parseParamBbcode('align', '(center|left|right|justify){1}', '<div style="text-alignement: $1">$2</div>', $text);
        // [size=$size][/size]
        $this->_parseParamBbcode('size', '([0-9].*)', '<span style="font-size: $1">$2</span>', $text);

        $this->_parseBbcodeEmail($text);
        $this->_parseBbcodeUrl($text);
        $this->_parseBbcodeImg($text);
        $this->_parseBbcodeSpoiler($text);
        $this->_parseScriptTags($text);

        $this->_parseSmiley($text);

        //[br]
        $this->_parseBbcodeBr($text);

        return $text;
    }

    /**
     * parse bbcode corespondig to $pattern inside $text and replace with $replace
     *
     * @param string $pattern
     * @param string|callback $replace
     * @param string $text
     * @uses preg_replace
     */
    protected function _replaceLoop($pattern, $replace, &$text)
    {
        while (preg_match($pattern, $text)) {
            if (is_string($replace)) {
                $text = preg_replace($pattern, $replace, $text);
            } else {
                $text = preg_replace_callback($pattern, $replace, $text);
            }
        }
    }


    protected function _parseSmiley(&$text)
    {
        $keys = array_keys($this->_smileys);
        $values = array_values($this->_smileys);
        $text = str_replace($this->_getSmileyKey(), $this->_getSmileyImg(), $text);
    }

    protected function _getSmileyKey()
    {
        $data = array();
        foreach ($this->_smileys as $key => $smiley) {
            $data[] = ":" . $smiley . ":";
        }
        return $data;
    }

    protected function _getSmileyImg()
    {
        $data = array();
        foreach ($this->_smileys as $key => $smiley) {
            $data[] = "<img src=\"".$this->_config["smiley_path"].$smiley.$this->_config["smiley_ext"]."\" alt=\"$smiley\" />"; //Original code
        }
        return $data;
    }


    protected function _parseSimpleBbcode($tag, $replace, &$text)
    {
        $this->_replaceLoop('#\[' . $tag . '\](.*?)\[/' . $tag . '\]#si', $replace, $text);
    }

    protected function _parseParamBbcode($tag, $param, $replace, &$text)
    {
        $this->_replaceLoop('#\[' . $tag . '=' . $param . '\](.*?)\[/' . $tag . '\]#si', $replace, $text);
    }

    //
    // Function to parse bbcode
    //
    // [br]
    protected function _parseBbcodeBr(&$text)
    {
        $text = preg_replace('#\[br\]#i', ' <br />', $text);
        $text = preg_replace('#\n#i', '<br />', $text);
    }

    // <script...>...</script>
    protected function _parseScriptTags(&$text)
    {
        $text = preg_replace('#<script(.*)>(.*)</script>#iU', '&lt;script$1&gt;$2&lt;/script&gt;', $text);
        $text = preg_replace('#<script(.*)>#iU', '&lt;script$1&gt;', $text);
    }

    // [img]http://www.site.tld/image.png[/img]
    protected function _parseBbcodeImg(&$text)
    {
        $nbMatches = preg_match('#\[img\](.*?)\[/img\]#si', $text, $matches);
        if ($nbMatches != 0) {
            $urlImage = $matches[1];
            if (preg_match('#(.*\.php.*)|(.*\.(x?)(ht)?m.*)|(.*\.js.*)#si', $urlImage, $array)) {
                $text = preg_replace_callback(
                    '#\[img\](.*?)\[/img\]#si',
                    function ($matches) {
                        return '<span>' . htmlspecialchars($matches[1]) . '</span>';
                    },
                    $text
                );
            } else {
                $text = preg_replace_callback(
                    '#\[img\](.*?)\[/img\]#si',
                    function ($matches) {
                        return '<img src="' . $matches[1] . '" alt="' . htmlspecialchars($matches[1]) . '" />"';
                    },
                    $text
                );
            }
        }
    }

    /**
     * Execute *before* other bbcode parse!
     * [nobbcode]txt[/nobbcode]
     * @param unknown_type $text
     * @return unknown
     */
    protected function _parseBbcodeNobbcode(&$text)
    {
        $text = preg_replace_callback(
            '#\[nobbcode\](.+?)\[/nobbcode\]#si',
            function ($matches) {
                return '<div class="bbcode_nobbcode">' . strtr($matches[1], array('[' => '&#91;', ']' => '&#93;')) . '</div>';
            },
            $text
        );
    }

    // [url]url[/url] & [url=url]url txt[/url]
    protected function _parseBbcodeUrl(&$text)
    {
        $text = preg_replace_callback(
            '#\[url\]([^ \"\t\n\r<]*?)\[/url\]#i',
            function ($matches) {
                return $this->_encodeUrl($matches[1], '', $this->_config['url_limit']);
            },
            $text
        );
        $text = preg_replace_callback(
            '#\[url=([^ \"\t\n\r<]*?)\](.*?)\[/url\]#si',
            function ($matches) {
                return $this->_encodeUrl($matches[1], $matches[2], $this->_config['url_limit']);
            },
            $text
        );
    }

    // [email]email[/email] & [email=email]email txt[/email]
    protected function _parseBbcodeEmail(&$text)
    {
        $this->_replaceLoop(
            '#\[email\]([^\[]*?)\[/email\]#si',
            function ($matches) {
                return $this->_encodeEmail($matches[1]);
            },
            $text
        );

        $this->_replaceLoop(
            '#\[email=([^\[]*?)\](.*?)\[/email\]#si',
            function ($matches) {
                return $this->_encodeEmail($matches[1], $matches[2]);
            },
            $text
        );
    }

    // [code]txt[/code] & [code=language]txt[/code]
    protected function _parseBbcodeCode(&$text)
    {
        $text = preg_replace_callback('#\n?\[code(=[a-zA-Z0-9]*?)?\](.+?)\[/code\]\n?#is', array($this, '_cbParseBbcodeCode'), $text);
    }

    protected function _cbParseBbcodeCode($match)
    {
        $pattern = array("\n", "\t", '  ', '[', ']', ')', '(', '<', '>');
        $replace = array('<br />', '&nbsp; &nbsp;&nbsp;', '&nbsp;&nbsp;', '&#91;', '&#93;', '&#41;', '&#40;', '&#60;', '&#62;');
        $text = str_replace($pattern, $replace, $match[2]);
        $code = '';
        if ($match[1] != '') {
            $code = '_' . substr($match[1], 1);
        }
        return '<div class="bbcode_code' . $code . '">' . $text . '</div>';
    }

    protected function _parseBbcodeList(&$text)
    {
        $pattern = '#\n?\[list=?(greek|square|circle|disc|I|i|A|a|1)?\](.+?)\[/list\]\n?#is';
        while (preg_match($pattern, $text)) {
            $text = preg_replace_callback($pattern, array($this, '_cbParseBbcodeList'), $text);
        }
    }

    protected function _parseBbcodeQuote(&$text)
    {
        //$this->_replaceLoop('#\n?\[quote\](.*)\[/quote\]\n?#mSi', '<blockquote>$1</blockquote>', $text);
        //$this->_replaceLoop('#\n?\[quote=(.*)\](.*)\[/quote\]\n?#mSi', '<blockquote><h5>$1</h5>$2</blockquote>', $text);
        $this->_replaceLoop('#\[quote\](.*)\[/quote\]#Usi', '<blockquote class="citation">$1</blockquote>', $text);
        $this->_replaceLoop('#\[quote=([^\]]*)\](.*)\[/quote\]#Usi', '<blockquote class="citation"><p class="bold">$1 :</p>$2</blockquote>', $text);
        //$this->_replaceLoop('#\[quote author=([^\]]*)\](.*)\[/quote\]#Usi', '<blockquote><p>$1 :</p>$2</blockquote>', $text);
    }

    protected function _parseBbcodeSpoiler(&$text)
    {
        $this->_replaceLoop('#\[spoiler\](.*)\[/spoiler\]#Usi', '<button class="btn_spoiler" type="button">spoiler</button><div>$1</div>', $text);
        $this->_replaceLoop('#\[spoiler=[ ]*\](.*)\[/spoiler\]#Usi', '<button class="btn_spoiler" type="button">spoiler</button><div>$1</div>', $text);
        $this->_replaceLoop('#\[spoiler=([^\]]*)\](.*)\[/spoiler\]#Usi', '<button class="btn_spoiler" type="button">$1</button><div>$2</div>', $text);
    }


    protected function _encodeUrl($url, $txt = '', $limit = 40)
    {
        // @TODO test url and check javascrip and other bad stuf
        $url_txt = ($txt != '') ? $txt : $url;
        $url_txt = str_replace(' &nbsp;', '  ', $url_txt); //comes back to double spaces to calculate the correct length
        if ($limit > 10 && strlen($url_txt) > $limit) {
            $url_txt = substr($url_txt, 0, $limit - 10) . '&#8230;' . substr($url_txt, -10);
        }
        // if URL starts like "javascript" (case insensitive), set it to # to broke XSS
        if (mb_strtolower(substr($url, 0, 10)) == "javascript") {
            $url = '#';
        }
        $url_txt = str_replace('  ', ' &nbsp;', $url_txt); //comes back again to ' &nbsp;' to respect multiple spaces
        return '<a href="' . $url . '">' . $url_txt . '</a>';
    }

    protected function _encodeEmail($email, $txt = '')
    {
        if (preg_match('#^\w([-_.]?\w)*@\w([-_.]?\w)*\.([a-z]{2,4})$#', $email)) {
            //mini anti robots
            $new = '';
            $len = strlen($email);
            for ($i = 0; $i < $len; $i++) {
                $new .= '&#x' . bin2hex($email{$i}) . ';';
            }

            //formating email mailto.
            return '<a href="mailto:' . $new . '">' . ($txt != '' ? $txt : $new) . '</a>';
        }
        return $email;
    }

    protected function _cbParseBbcodeList($match)
    {
        $text = '<ul>';
        $text_end = '</ul>';
        if ($match[1] != '') {
            switch ($match[1]) {
                case 'disk':
                case 'circle':
                case 'square':
                    $text = '<ul type="' . $match[1] . '">';
                    break;
                case 'i':
                case 'I':
                case 'A':
                case 'a':
                case '1':
                    $text = '<ol type="' . $match[1] . '">';
                    $text_end = '</ol>';
                    break;
            }
        }
        $this->_replaceLoop('#\n?(\[\*\](.*))\n?#is', '<li>$2</li>', $match[2]);
        $text .= $match[2];
        return $text . $text_end;
    }
}
