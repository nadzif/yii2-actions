<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 6/24/2018
 * Time: 8:20 AM
 */

namespace nadzif\actions\widgets;

class Modal extends \yii\bootstrap4\Modal
{
    const SIZE_LARGE   = "modal-lg w-100";
    const SIZE_SMALL   = "modal-sm";
    const SIZE_DEFAULT = "";

    public $options       = ['tabindex' => false];
    public $headerOptions = ['class' => 'pd-y-20 pd-x-25'];
    public $toggleButton  = [
        'label' => '<i class="fas fa-plus"></i>',
        'class' => 'btn btn-success'
    ];

    public $size = self::SIZE_LARGE;

}