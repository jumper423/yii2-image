<?php

namespace jumper423;

use yii\base\Exception;
use yii\base\Object;

class Image extends Object
{
    /**
     * Обрезать изображение
     *
     * $x_o и $y_o - координаты левого верхнего угла выходного изображения на исходном
     * $w_o и h_o - ширина и высота выходного изображения
     *
     * @param $image
     * @param $x_o
     * @param $y_o
     * @param $w_o
     * @param $h_o
     * @return bool
     * @throws Exception
     */
    public static function crop($image, $x_o, $y_o, $w_o, $h_o) {
        if (($x_o < 0) || ($y_o < 0) || ($w_o < 0) || ($h_o < 0)) {
            throw new Exception("Некорректные входные параметры");
        }
        list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)
        $types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
        if ($ext) {
            $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
            $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
        } else {
            throw new Exception('Некорректное изображение'); // Выводим ошибку, если формат изображения недопустимый
        }
        if ($x_o + $w_o > $w_i) $w_o = $w_i - $x_o; // Если ширина выходного изображения больше исходного (с учётом x_o), то уменьшаем её
        if ($y_o + $h_o > $h_i) $h_o = $h_i - $y_o; // Если высота выходного изображения больше исходного (с учётом y_o), то уменьшаем её
        $img_o = imagecreatetruecolor($w_o, $h_o); // Создаём дескриптор для выходного изображения
        imagecopy($img_o, $img_i, 0, 0, $x_o, $y_o, $w_o, $h_o); // Переносим часть изображения из исходного в выходное
        $func = 'image'.$ext; // Получаем функция для сохранения результата
        return $func($img_o, $image); // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
    }

    /**
     * Сделать картинку уникальной
     *
     * @param $image
     * @return string
     * @throws Exception
     */
    public static function unique($image) {
        list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)
        $types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
        if ($ext) {
            $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
            $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
        } else {
            throw new Exception('Некорректное изображение'); // Выводим ошибку, если формат изображения недопустимый
        }
        $filterArgument1 = self::imageFilterArgument(rand(0,12));
        $filterArgument2 = self::imageFilterArgument(rand(0,12));

        if (!is_null($filterArgument1)) {
            imagefilter($img_i, $filterArgument1[0], isset($filterArgument1[1]) ? $filterArgument1[1] : null);
        }
        if (!is_null($filterArgument2)) {
            imagefilter($img_i, $filterArgument2[0], isset($filterArgument2[1]) ? $filterArgument2[1] : null);
        }

        $path = \Yii::getAlias('@runtime/tmp/' . strtr(\Yii::$app->security->generateRandomString(), ['-' => '', '_' => '']) . '.' . $ext);

        $func = 'image'.$ext; // Получаем функция для сохранения результата
        $func($img_i, $path);
        return $path; // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
    }

    /**
     * @param $filterType
     * @return array|null
     */
    private static function imageFilterArgument($filterType) {
        switch ($filterType % 6) {
            case 0:
                return [
                    IMG_FILTER_BRIGHTNESS,
                    rand(-3, 3),
                ];
            case 1:
                return [
                    IMG_FILTER_CONTRAST,
                    rand(-4,4),
                ];
            case  2:
                return null;
                /*return [
                    IMG_FILTER_GAUSSIAN_BLUR,
                ];*/
            case  3:
                return null;
                /*return [
                    IMG_FILTER_SELECTIVE_BLUR,
                ];*/
            case  4:
                return null;
                /*return [
                    IMG_FILTER_SMOOTH,
                    rand(-3,3),
                ];*/
            case 5:
                if ($filterType == 11) {
                    return [
                        IMG_FILTER_GRAYSCALE,
                    ];
                }
        }
        return null;
    }
}