<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types = 1);

namespace Kajona\System\View\Components\Stackeddatabar;

use Kajona\System\System\GraphCommons;
use Kajona\System\View\Components\AbstractComponent;

/**
 * Shows stacked bar "chart" for set data
 *
 * @author andrii.konoval@artemeon.de
 * @since 7.0
 * @componentTemplate core/module_system/view/components/stackeddatabar/template.twig
 */
class StackedDataBar extends AbstractComponent
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $colors;

    /**
     * @var array
     */
    protected $labels;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int;
     */
    protected $width;

    private $defaultColors = [
        "#8bbc21", "#2f7ed8", "#f28f43", "#1aadce", "#77a1e5", "#0d233a", "#c42525", "#a6c96a", "#910000",
        '#0048Ba', '#B0BF1A', '#C46210', '#FFBF00', '#9966CC', '#841B2D', '#FAEBD7', '#8DB600', '#D0FF14', '#FF9966', '#007FFF', '#FF91AF', '#E94196', '#CAE00D', '#54626F'
    ];

    /**
     * StackedDataBar constructor.
     * @param string $title
     * @param array $data
     * @param array $colors
     * @param array $labels
     * @param int $width
     */
    public function __construct(string $title, array $data, array $colors = [], array $labels = [], int $width = 0)
    {
        parent::__construct();

        $this->setTitle($title);
        $this->setData($data);
        $this->setColors($colors);
        $this->setLabels($labels);
        $this->setWidth($width);
    }

    /**
     * @inheritdoc
     */
    public function renderComponent(): string
    {
        $resultData = [];
        $colorSet = count($this->colors) >= count($this->data) ? $this->colors : $this->defaultColors;
        $dataSum = 0;
        $uniqueKey = md5(microtime().rand());
        foreach ($this->data as $index => $objDataPiont) {
            $dataValue = $objDataPiont->getFloatValue();
            $dataSum += $dataValue;
            if (!empty($objDataPiont)) {
                $resultData[$index] = [
                    'value' => round($dataValue, 2),
                    'color' => $colorSet[$index],
                    'label' => isset($this->labels[$index]) ? $this->labels[$index] : "",
                    'class' => str_replace(' ', '', $this->title).'-'.$index.'-'.$uniqueKey.'-box',
                    'dataPointHandler' => $objDataPiont->getObjActionHandlerValue()
                ];
            }
        }
        if ($dataSum == 0) {
            $resultData = [];
        }
        foreach ($resultData as $index => $data) {
            $resultData[$index]['proc'] = round($data['value'] / $dataSum * 100, 1);
        }
        $data = [
            "data" => $resultData,
            "title" => $this->title,
            "width" => $this->width
        ];

        return $this->renderTemplate($data);
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = GraphCommons::convertArrValuesToDataPointArray($data);;
    }

    /**
     * @param array $colors
     */
    public function setColors(array $colors): void
    {
        $this->colors = $colors;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        if (is_numeric($width) && $width != 0) {
            $this->width = $width.'px';
        } else {
            $this->width = '100%';
        }
    }

    /**
     * @param array $labels
     */
    public function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }

}
