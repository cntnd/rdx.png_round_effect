<?php

/**
 * Runde Ecken für transparente PNG.
 *
 * @author tdascoli
 * @author staabm
 *
 */

class rex_effect_png_round extends rex_effect_abstract
{
    public function execute()
    {
        $this->media->asImage();
        $gdimage = $this->media->getImage();
        $w = (int) $this->media->getWidth();
        $h = (int) $this->media->getHeight();

        $r_tl = (int)$this->params['topleft'];
        $r_tr = (int)$this->params['topright'];
        $r_bl = (int)$this->params['bottomright'];
        $r_br = (int)$this->params['bottomleft'];

        $q = 5; # change this if you want
        $r_tl *= $q;
        $r_tr *= $q;
        $r_bl *= $q;
        $r_br *= $q;

        # find unique color
        do {
            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);
        } while (imagecolorexact($gdimage, $r, $g, $b) < 0);

        $nw = $w * $q;
        $nh = $h * $q;

        $cornerImage = imagecreatetruecolor($nw, $nh);
        $alphacolor = imagecolorallocatealpha($cornerImage, $r, $g, $b, 127);
        imagealphablending($cornerImage, false);
        imagesavealpha($cornerImage, true);
        imagefilledrectangle($cornerImage, 0, 0, $nw, $nh, $alphacolor);

        imagefill($cornerImage, 0, 0, $alphacolor);
        imagecopyresampled($cornerImage, $gdimage, 0, 0, 0, 0, $nw, $nh, $w, $h);

        // topleft
        imagearc($cornerImage, $r_tl - 1, $r_tl - 1, $r_tl * 2, $r_tl * 2, 180, 270, $alphacolor);
        imagefilltoborder($cornerImage, 0, 0, $alphacolor, $alphacolor);
        // topright
        imagearc($cornerImage, $nw - $r_tr, $r_tr - 1, $r_tr * 2, $r_tr * 2, 270, 0, $alphacolor);
        imagefilltoborder($cornerImage, $nw - 1, 0, $alphacolor, $alphacolor);

        // bottomleft
        imagearc($cornerImage, $r_bl - 1, $nh - $r_bl, $r_bl * 2, $r_bl * 2, 90, 180, $alphacolor);
        imagefilltoborder($cornerImage, 0, $nh - 1, $alphacolor, $alphacolor);

        // bottomright
        imagearc($cornerImage, $nw - $r_br, $nh - $r_br, $r_br * 2, $r_br * 2, 0, 90, $alphacolor);
        imagefilltoborder($cornerImage, $nw - 1, $nh - 1, $alphacolor, $alphacolor);

        imagealphablending($cornerImage, true);
        imagecolortransparent($cornerImage, $alphacolor);

        // resize image down
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        imagefilledrectangle($img, 0, 0, $w, $h, $alphacolor);
        imagecopyresampled($img, $cornerImage, 0, 0, 0, 0, $w, $h, $nw, $nh);

        // output image
        $des = $img;
        $this->keepTransparent($des);
        $this->media->setImage($des);
        $this->media->refreshImageDimensions();
    }

    public function getName()
    {
        return rex_i18n::msg('pnground_png_round');
    }

    public function getParams()
    {
        return [
            [
                'label' => rex_i18n::msg('pnground_png_round_topleft'),
                'name' => 'topleft',
                'type' => 'int',
            ],
            [
                'label' => rex_i18n::msg('pnground_png_round_topright'),
                'name' => 'topright',
                'type' => 'int',
            ],
            [
                'label' => rex_i18n::msg('pnground_png_round_bottomleft'),
                'name' => 'bottomleft',
                'type' => 'int',
            ],
            [
                'label' => rex_i18n::msg('pnground_png_round_bottomright'),
                'name' => 'bottomright',
                'type' => 'int',
            ],
        ];
    }
}