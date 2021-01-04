<?php
declare(strict_types=1);

namespace phpu\letteravatar;

use GDText\Box;
use GDText\Color;

class Avatar
{
    private $config = [
        'only-letter' => false, // 是否只用字母
        'default-userName' => 'wmstudio', // 默认的用户名
        'size' => [200, 200], // 头像尺寸(px)
    ];

    private $avatar;

    /**
     * 用户名全名
     * @var string
     */
    private $userNameFull = '';

    /**
     * 头像中字符个数
     * @var int
     */
    private $initialsLen = 1;

    /**
     * 缩略字符
     * @var string
     */
    private $initials = '';

    /**
     * 文字颜色
     * @var int[]
     */
    private $textColor = [0, 10, 18];

    /**
     * 背景色
     * @var int[]
     */
    private $backgroundColor = [79, 91, 98];

    /**
     * 字体文件路径
     * @var string
     */
    private $fontFile = '';


    /**
     * 颜色 [[浅][深]]
     * @var int[][][]
     */
    public $defaultColors = [
        [[229, 115, 115], [183, 28, 28]], [[211, 47, 47], [127, 0, 0]], [[244, 67, 54], [127, 0, 0]], [[229, 57, 53], [127, 0, 0]],
        [[240, 98, 146], [136, 14, 79]], [[194, 24, 91], [86, 0, 39]], [[197, 17, 98], [86, 0, 39]], [[188, 71, 123], [86, 0, 39]],
        [[186, 104, 200], [74, 20, 140]], [[123, 31, 162], [18, 0, 94]], [[156, 39, 176], [18, 0, 94]], [[124, 67, 189], [18, 0, 94]],
        [[149, 117, 205], [49, 27, 146]], [[81, 45, 168], [0, 0, 99]], [[103, 58, 183], [0, 0, 99]], [[103, 70, 195], [0, 0, 99]],
        [[121, 134, 203], [26, 35, 126]], [[63, 81, 181], [0, 0, 81]], [[48, 63, 159], [0, 0, 81]], [[83, 75, 174], [0, 0, 81]],
        [[100, 181, 246], [13, 71, 161]], [[33, 150, 243], [0, 33, 113]], [[25, 118, 210], [0, 33, 113]], [[84, 114, 211], [0, 33, 113]],
        [[79, 195, 247], [1, 87, 155]], [[3, 169, 244], [1, 87, 155]], [[2, 136, 209], [0, 47, 108]], [[79, 131, 204], [0, 47, 108]],
        [[77, 208, 225], [0, 96, 100]], [[0, 188, 212], [0, 96, 100]], [[0, 151, 167], [0, 54, 58]], [[66, 142, 146], [0, 54, 58]],
        [[77, 182, 172], [0, 77, 64]], [[0, 150, 136], [0, 37, 26]], [[0, 121, 107], [0, 37, 26]], [[57, 121, 107], [0, 37, 26]],
        [[129, 199, 132], [27, 94, 32]], [[76, 175, 80], [27, 94, 32]], [[56, 142, 60], [0, 51, 0]], [[76, 140, 74], [0, 51, 0]],
        [[174, 213, 129], [51, 105, 30]], [[139, 195, 74], [51, 105, 30]], [[104, 159, 56], [0, 61, 0]], [[98, 151, 73], [0, 61, 0]],
        [[220, 231, 117], [130, 119, 23]], [[205, 220, 57], [82, 76, 0]], [[175, 180, 43], [82, 76, 0]], [[180, 166, 71], [82, 76, 0]],
        [[255, 241, 118], [245, 127, 23]], [[255, 235, 59], [245, 127, 23]], [[251, 192, 45], [188, 81, 0]], [[255, 176, 76], [188, 81, 0]],
        [[255, 213, 79], [255, 111, 0]], [[255, 193, 7], [196, 62, 0]], [[255, 160, 0], [196, 62, 0]], [[255, 160, 64], [196, 62, 0]],
        [[255, 183, 77], [230, 81, 0]], [[255, 152, 0], [172, 25, 0]], [[245, 124, 0], [172, 25, 0]], [[255, 131, 58], [172, 25, 0]],
        [[255, 138, 101], [191, 54, 12]], [[255, 87, 34], [135, 0, 0]], [[249, 104, 58], [135, 0, 0]],
        [[161, 136, 127], [62, 39, 35]], [[121, 85, 72], [27, 0, 0]], [[106, 79, 75], [27, 0, 0]],
        [[144, 164, 174], [38, 50, 56]], [[96, 125, 139], [0, 10, 18]], [[79, 91, 98], [0, 10, 18]],
    ];

    /**
     * 构造方法
     * Avatar constructor.
     */
    public function __construct()
    {
        // 随机颜色
        $this->randColor();
    }

    /**
     * 对象拷贝
     */
    public function __clone()
    {
    }

    /**
     * 设置用户名
     *
     * @param string $userName
     * @return $this
     */
    public function userName(string $userName):Avatar {
        $this->setUserName($userName);
        return $this;
    }

    /**
     * 指定头像中截取字符的个数
     *
     * @param int $len
     * @return $this
     */
    public function len(int $len):Avatar {
        $this->setLen($len);
        return $this;
    }

    /**
     * 设置颜色，字符颜色和背景颜色都需要设置，否则随机一组颜色
     *
     * @param string|array $textColor 字符颜色，或者是数组[字符颜色,背景颜色]
     * @param string|null $backgroundColor 背景颜色
     * @return $this
     */
    public function color($textColor, string $backgroundColor = null):Avatar {
        if (is_array($textColor) && count($textColor) === 2 && is_null($backgroundColor)) {
            list($textColor, $backgroundColor) = $textColor;
        }
        $this->setColor($textColor, $backgroundColor);
        return $this;
    }


    /**
     * 生成头像
     *
     * @param string $userName 用户名全名
     * @param array $color 头像颜色数组[文字颜色,背景颜色]
     * @param int $len 头像中截取的字符数
     * @return false|string
     */
    private function makeAvatar($userName = '', array $color = [], int $len = 0)
    {
        if (!empty($userName)) {
            if (is_int($userName) && $len <= 0) $this->setLen($userName);
            else if (is_array($userName) && empty($color)) $this->setColor($color[0], $color[1]);
            else $this->setUserName($userName);
        }
        if ($len > 0) {
            $this->setLen($len);
        }
        if (!empty($color) && count($color) === 2) {
            $this->setColor($color[0], $color[1]);
        }


        // 字体所在目录
        $font_root_path = __DIR__ . DIRECTORY_SEPARATOR . '../assets/fonts';

        // 头像字符
        $this->generatedInitials();

        // 头像最小尺寸 (px)
        $min_size = min($this->config['size']);

        // 头像
        $this->avatar = imagecreatetruecolor($this->config['size'][0], $this->config['size'][1]);

        // 背景色
        $background_color = imagecolorallocate($this->avatar, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);

        // 画一个方形并填背景色
        imagefill($this->avatar, 0, 0, $background_color);

        // 边距
        $padding = 30 * ($min_size / 256);
        $font_size = ($min_size - $padding * 2) / $this->initialsLen;

        $fontFile = '';
        if (!$this->isLetter()) {
            //中文字体左偏移
            $boxX = ($font_size / 12) * -1;
            $boxY = 0;
            $fontFile = $font_root_path . DIRECTORY_SEPARATOR . 'NotoSansSC-Medium.otf';
        } else {
            $boxX = 0;
            $boxY = 0;
            $fontFile = $font_root_path . DIRECTORY_SEPARATOR . 'Roboto-Medium.ttf'; // Heebo-Medium.ttf
        }

        $box = new Box($this->avatar);
        $box->setFontFace($fontFile); // http://www.dafont.com/pacifico.font
        $box->setFontSize($font_size);
        $box->setFontColor(new Color($this->textColor[0], $this->textColor[1], $this->textColor[2]));
        //$box->setTextShadow(new Color(0, 0, 0, 50), 0, -2);
        $box->setBox($boxX, $boxY, $this->config['size'][0], $this->config['size'][1]);
        $box->setTextAlign('center', 'center');
        $box->draw($this->initials);

        ob_start();
        imagepng($this->avatar);
        $printContent = ob_get_clean();
        ob_end_clean();
        imagedestroy($this->avatar);

        return $printContent;
    }

    /**
     * 销毁头像
     *
     * @return bool
     */
    public function freeAvatar()
    {
        return imagedestroy($this->avatar);
    }

    /**
     * png格式显示头像
     *
     * @param string $userName
     * @param array $color
     * @param int $len
     */
    public function printPng(string $userName = '', array $color = [], int $len = 0)
    {
        $printContent = $this->makeAvatar($userName, $color, $len);

        header('Content-Type: image/png');
        header('Content-Length: ' . strlen($printContent) . '');
        echo $printContent;
    }

    /**
     * 设置用户名
     * @param string $userName
     */
    private function setUserName(string $userName): void
    {
        if (false != $userName = $this->validateUserName($userName)) {
            $this->userNameFull = $userName;
        } else {
            $this->userNameFull = $this->config['default-userName'];
        }
    }

    /**
     * 处理并验证用户名
     * @param string $userName
     * @return false|string
     */
    private function validateUserName(string $userName)
    {
        $userName = preg_replace(['/\p{Z}/u', '/\p{N}/u', '/\p{S}/u', '/\p{P}/u', '/\p{C}/u'], '', trim($userName));

        return mb_strlen($userName, 'UTF-8') > 0 ? mb_strtoupper($userName, 'UTF-8') : false;
    }

    /**
     * 指定头像中截取字符的个数
     * @param int $len 截取字符的个数
     */
    private function setLen(int $len): void
    {
        $max = mb_strlen($this->userNameFull, 'UTF-8');
        if ($len > $max) {
            $len = $max;
        }
        $this->initialsLen = $len;
    }

    /**
     * 缩略字符
     *
     * @return string
     */
    private function generatedInitials()
    {
        return $this->initials = mb_substr($this->userNameFull, 0, $this->initialsLen, 'UTF-8');
    }

    /**
     * 是否为字母
     * @return false|int
     */
    private function isLetter()
    {
        return preg_match('/[a-zA-Z]/', $this->initials);
    }

    /**
     * 设置颜色，字符颜色和背景颜色都需要设置，否则随机一组颜色
     *
     * @param string $textColor 16进制颜色值
     * @param string $backgroundColor 16进制颜色值
     */
    private function setColor(string $textColor, string $backgroundColor): void
    {

        $e = false;

        if (empty($textColor) || empty($backgroundColor)) {
            $e = true;
        } else {
            if (strpos($textColor, '#') === 0
                && (strlen($textColor) === 7 || strlen($textColor) === 4)) {
                $this->textColor = self::hex2rgb($textColor);
            } else if (substr_count($textColor, ',') === 2) {
                $this->textColor = explode(',', $textColor);
            } else {
                $e = true;
            }

            if (strpos($backgroundColor, '#') === 0
                && (strlen($backgroundColor) === 7 || strlen($backgroundColor) === 4)) {
                $this->backgroundColor = self::hex2rgb($backgroundColor);
            } else if (substr_count($backgroundColor, ',') === 2) {
                $this->backgroundColor = explode(',', $backgroundColor);
            } else {
                $e = true;
            }
        }

        if ($e) {
            $this->randColor();
        }
    }

    /**
     * 随机颜色
     */
    private function randColor(): void
    {
        $max = count($this->defaultColors) - 1;
        $i = mt_rand(0, $max);
        $color = $this->defaultColors[$i];

        $ii = mt_rand(0, 9) % 2;
        if ($ii === 0) {
            $this->textColor = $color[0];
            $this->backgroundColor = $color[1];
        } else {
            $this->textColor = $color[1];
            $this->backgroundColor = $color[0];
        }
    }

    /**
     * 将十六进制的颜色代码转为RGB
     * @param string $hexColor 十六进制颜色代码
     * @return int[] RGB颜色数组[r,g,b]
     */
    protected static function hex2rgb(string $hexColor)
    {
        if ($hexColor[0] == '#') $hexColor = substr($hexColor, 1);
        $hexColor = preg_replace("/[^0-9A-Fa-f]/", '', $hexColor);
        if (strlen($hexColor) == 3) {
            $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
        }
        $int = hexdec($hexColor);
        return [0xFF & ($int >> 0x10), 0xFF & ($int >> 0x8), 0xFF & $int];
    }

}