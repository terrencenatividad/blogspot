<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2017 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 * @version   2.0.3
 */

/**
 * Class representing a rectangle
 *
 * @package setasign\Fpdi\PdfReader\DataStructure
 */
class Rectangle
{
    /**
     * @var int|float
     */
    protected $llx;

    /**
     * @var int|float
     */
    protected $lly;

    /**
     * @var int|float
     */
    protected $urx;

    /**
     * @var int|float
     */
    protected $ury;

    /**
     * Create a rectangle instance by a PdfArray.
     *
     * @param PdfArray|mixed $array
     * @param PdfParser $parser
     * @return Rectangle
     */
    public static function byPdfArray($array, PdfParser $parser)
    {
        $array = PdfArray::ensure(PdfType::resolve($array, $parser), 4)->value;
        $ax = PdfNumeric::ensure(PdfType::resolve($array[0], $parser))->value;
        $ay = PdfNumeric::ensure(PdfType::resolve($array[1], $parser))->value;
        $bx = PdfNumeric::ensure(PdfType::resolve($array[2], $parser))->value;
        $by = PdfNumeric::ensure(PdfType::resolve($array[3], $parser))->value;

        return new self($ax, $ay, $bx, $by);
    }

    /**
     * Rectangle constructor.
     *
     * @param float|int $ax
     * @param float|int $ay
     * @param float|int $bx
     * @param float|int $by
     */
    public function __construct($ax, $ay, $bx, $by)
    {
        $this->llx = min($ax, $bx);
        $this->lly = min($ay, $by);
        $this->urx = max($ax, $bx);
        $this->ury = max($ay, $by);
    }

    /**
     * Get the width of the rectangle.
     *
     * @return float|int
     */
    public function getWidth()
    {
        return $this->urx - $this->llx;
    }

    /**
     * Get the height of the rectangle.
     *
     * @return float|int
     */
    public function getHeight()
    {
        return $this->ury - $this->lly;
    }

    /**
     * Get the lower left abscissa.
     *
     * @return float|int
     */
    public function getLlx()
    {
        return $this->llx;
    }

    /**
     * Get the lower left ordinate.
     *
     * @return float|int
     */
    public function getLly()
    {
        return $this->lly;
    }

    /**
     * Get the upper right abscissa.
     *
     * @return float|int
     */
    public function getUrx()
    {
        return $this->urx;
    }

    /**
     * Get the upper right ordinate.
     *
     * @return float|int
     */
    public function getUry()
    {
        return $this->ury;
    }

    /**
     * Get the rectangle as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            $this->llx,
            $this->lly,
            $this->urx,
            $this->ury
        );
    }

    /**
     * Get the rectangle as a PdfArray.
     *
     * @return PdfArray
     */
    public function toPdfArray()
    {
        $array = new PdfArray();
        $array->value[] = PdfNumeric::create($this->llx);
        $array->value[] = PdfNumeric::create($this->lly);
        $array->value[] = PdfNumeric::create($this->urx);
        $array->value[] = PdfNumeric::create($this->ury);

        return $array;
    }
}
