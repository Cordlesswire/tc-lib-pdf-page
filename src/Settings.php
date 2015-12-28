<?php
/**
 * Settings.php
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 *
 * This file is part of tc-lib-pdf-page software library.
 */

namespace Com\Tecnick\Pdf\Page;

use \Com\Tecnick\Color\Pdf as Color;
use \Com\Tecnick\Pdf\Page\Exception as PageException;

/**
 * Com\Tecnick\Pdf\Page\Settings
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class Settings extends \Com\Tecnick\Pdf\Page\Box
{
    /**
     * Sanitize or set the page modification time.
     *
     * @param array $data Page data
     */
    public function sanitizePageNumber(array &$data)
    {
        if (!empty($data['num'])) {
            $data['num'] = max(0, intval($data['num']));
        }
    }

    /**
     * Sanitize or set the page modification time.
     *
     * @param array $data Page data
     */
    public function sanitizeTime(array &$data)
    {
        if (empty($data['time'])) {
            $data['time'] = time();
        } else {
            $data['time'] = max(0, intval($data['time']));
        }
    }

    /**
     * Sanitize or set the page group
     *
     * @param array $data Page data
     */
    public function sanitizeGroup(array &$data)
    {
        if (empty($data['group'])) {
            $data['group'] = 0;
        } else {
            $data['group'] = intval($data['group']);
        }
    }

    /**
     * Sanitize or set the page content.
     *
     * @param array $data Page data
     */
    public function sanitizeContent(array &$data)
    {
        if (empty($data['content'])) {
            $data['content'] = array('');
        } else {
            $data['content'] = array((string)$data['content']);
        }
    }

    /**
     * Sanitize or set the annotation references
     *
     * @param array $data Page data
     */
    public function sanitizeAnnotRefs(array &$data)
    {
        if (empty($data['annotrefs'])) {
            $data['annotrefs'] = array();
        }
    }

    /**
     * Sanitize or set the page rotation.
     * The number of degrees by which the page shall be rotated clockwise when displayed or printed.
     * The value shall be a multiple of 90.
     *
     * @param array $data Page data
     */
    public function sanitizeRotation(array &$data)
    {
        if (empty($data['rotation']) || (($data['rotation'] % 90) != 0)) {
            $data['rotation'] = 0;
        } else {
            $data['rotation'] = intval($data['rotation']);
        }
    }

    /**
     * Sanitize or set the page preferred zoom (magnification) factor.
     *
     * @param array $data Page data
     */
    public function sanitizeZoom(array &$data)
    {
        if (empty($data['zoom'])) {
            $data['zoom'] = 1;
        } else {
            $data['zoom'] = floatval($data['zoom']);
        }
    }

    /**
     * Sanitize or set the page transitions.
     *
     * @param array $data Page data
     */
    public function sanitizeTransitions(array &$data)
    {
        if (empty($data['transition'])) {
            return;
        }
        // display duration before advancing page
        if (empty($data['transition']['Dur'])) {
            unset($data['transition']['Dur']);
        } else {
            $data['transition']['Dur'] = floatval($data['transition']['Dur']);
        }
        // transition style
        $styles = array(
            'Split',
            'Blinds',
            'Box',
            'Wipe',
            'Dissolve',
            'Glitter',
            'R',
            'Fly',
            'Push',
            'Cover',
            'Uncover',
            'Fade'
        );
        if (empty($data['transition']['S']) || !in_array($data['transition']['S'], $styles)) {
            $data['transition']['S'] = 'R';
        }
        // duration of the transition effect, in seconds
        if (!isset($data['transition']['D'])) {
            $data['transition']['D'] = 1;
        } else {
            $data['transition']['D'] = intval($data['transition']['D']);
        }
        // dimension in which the specified transition effect shall occur
        if (empty($data['transition']['Dm'])
            || !in_array($data['transition']['S'], array('Split', 'Blinds'))
            || !in_array($data['transition']['Dm'], array('H', 'V'))
        ) {
            unset($data['transition']['Dm']);
        }
        // direction of motion for the specified transition effect
        if (empty($data['transition']['M'])
            || !in_array($data['transition']['S'], array('Split', 'Box', 'Fly'))
            || !in_array($data['transition']['M'], array('I', 'O'))
        ) {
            unset($data['transition']['M']);
        }
        // direction in which the specified transition effect shall moves
        if (empty($data['transition']['Di'])
            || !in_array($data['transition']['S'], array('Wipe', 'Glitter', 'Fly', 'Cover', 'Uncover', 'Push'))
            || !in_array($data['transition']['Di'], array('None', 0, 90, 180, 270, 315))
            || (in_array($data['transition']['Di'], array(90, 180)) && ($data['transition']['S'] != 'Wipe'))
            || (($data['transition']['Di'] == 315) && ($data['transition']['S'] != 'Glitter'))
            || (($data['transition']['Di'] == 'None') && ($data['transition']['S'] != 'Fly'))
        ) {
            unset($data['transition']['Di']);
        }
        // starting or ending scale at which the changes shall be drawn
        if (isset($data['transition']['SS'])) {
            $data['transition']['SS'] = floatval($data['transition']['SS']);
        }
        // If true, the area that shall be flown in is rectangular and opaque
        if (empty($data['transition']['B'])) {
            $data['transition']['B'] = false;
        } else {
            $data['transition']['B'] = true;
        }
    }

    /**
     * Sanitize or set the page margins
     *
     * @param array $data Page data
     */
    public function sanitizeMargins(array &$data)
    {
        if (empty($data['margin'])) {
            $data['margin'] = array();
        }
        $margins = array(
            'PL' => $data['width'],
            'PR' => $data['width'],
            'PT' => $data['height'],
            'HB' => $data['height'],
            'CT' => $data['height'],
            'CB' => $data['height'],
            'FT' => $data['height'],
            'PB' => $data['height']
        );
        foreach ($margins as $type => $max) {
            if (empty($data['margin'][$type])) {
                $data['margin'][$type] = 0;
            } else {
                $data['margin'][$type] = min(max(0, floatval($data['margin'][$type])), $max);
            }
        }
        $data['margin']['PR'] = min($data['margin']['PR'], ($data['width'] - $data['margin']['PL']));
        $data['margin']['HB'] = max($data['margin']['HB'], $data['margin']['PT']);
        $data['margin']['CT'] = max($data['margin']['CT'], $data['margin']['HB']);
        $data['margin']['CB'] = min($data['margin']['CB'], ($data['height'] - $data['margin']['CT']));
        $data['margin']['FT'] = min($data['margin']['FT'], $data['margin']['CB']);
        $data['margin']['PB'] = min($data['margin']['PB'], $data['margin']['FT']);

        $data['ContentWidth'] = ($data['width'] - $data['margin']['PL'] - $data['margin']['PR']);
        $data['ContentHeight'] = ($data['height'] - $data['margin']['CT'] - $data['margin']['CB']);
        $data['HeaderHeight'] = ($data['margin']['HB'] - $data['margin']['PT']);
        $data['FooterHeight'] = ($data['margin']['FT'] - $data['margin']['PB']);
    }

    /**
     * Sanitize or set the page boxes containing the page boundaries.
     *
     * @param array $data Page data
     */
    public function sanitizeBoxData(array &$data)
    {
        if (empty($data['box'])) {
            $data['box'] = $this->setPageBoxes($data['pwidth'], $data['pheight']);
        } else {
            if ($data['format'] == 'MediaBox') {
                $data['format'] = '';
                $data['width'] = abs($data['box']['MediaBox']['urx'] - $data['box']['MediaBox']['llx']) / $this->kunit;
                $data['height'] = abs($data['box']['MediaBox']['ury'] - $data['box']['MediaBox']['lly']) / $this->kunit;
                $this->setPageFormat($data);
            }
            if (empty($data['box']['MediaBox'])) {
                $data['box'] = $this->setBox($data['box'], 'MediaBox', 0, 0, $data['pwidth'], $data['pheight']);
            }
            if (empty($data['box']['CropBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'CropBox',
                    $data['box']['MediaBox']['llx'],
                    $data['box']['MediaBox']['lly'],
                    $data['box']['MediaBox']['urx'],
                    $data['box']['MediaBox']['ury']
                );
            }
            if (empty($data['box']['BleedBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'BleedBox',
                    $data['box']['CropBox']['llx'],
                    $data['box']['CropBox']['lly'],
                    $data['box']['CropBox']['urx'],
                    $data['box']['CropBox']['ury']
                );
            }
            if (empty($data['box']['TrimBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'TrimBox',
                    $data['box']['CropBox']['llx'],
                    $data['box']['CropBox']['lly'],
                    $data['box']['CropBox']['urx'],
                    $data['box']['CropBox']['ury']
                );
            }
            if (empty($data['box']['ArtBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'ArtBox',
                    $data['box']['CropBox']['llx'],
                    $data['box']['CropBox']['lly'],
                    $data['box']['CropBox']['urx'],
                    $data['box']['CropBox']['ury']
                );
            }
        }
        if ($data['orientation'] != $this->getPageOrientation(
            abs($data['box']['MediaBox']['urx'] - $data['box']['MediaBox']['llx']),
            abs($data['box']['MediaBox']['ury'] - $data['box']['MediaBox']['lly'])
        )) {
            $data['box'] = $this->swapCoordinates($data['box']);
        }
    }

    /**
     * Sanitize or set the page format
     *
     * @param array $data Page data
     */
    public function sanitizePageFormat(array &$data)
    {
        if (empty($data['orientation'])) {
            $data['orientation'] = '';
        }
        if (!empty($data['format'])) {
            list($data['width'], $data['height'], $data['orientation']) = $this->getPageFormatSize(
                $data['format'],
                $data['orientation'],
                $this->kunit
            );
        } else {
            $data['format'] = 'CUSTOM';
            if (empty($data['width']) || empty($data['height'])) {
                if (empty($data['box']['MediaBox'])) {
                    // default page format
                    $data['format'] = 'A4';
                    $data['orientation'] = 'P';
                    return $this->sanitizePageFormat($data);
                }
                $data['format'] = 'MediaBox';
                return;
            } else {
                list($data['width'], $data['height'], $data['orientation']) = $this->getPageOrientedSize(
                    $data['width'],
                    $data['height'],
                    $data['orientation']
                );
            }
        }
        // convert values in points
        $data['pwidth'] = ($data['width'] * $this->kunit);
        $data['pheight'] = ($data['height'] * $this->kunit);
    }
}