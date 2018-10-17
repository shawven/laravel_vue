<?php
/**
 * Created by PhpStorm.
 * @description: 关键字装饰器
 * @author: FS
 * @date: 2018-04-13 13:44
 */

namespace App\Http\Helpers\Selector\Decorator;

use App\Http\Helpers\Selector\Parser\AssociationParser;
use App\Http\Helpers\Selector\Parser\CommonParser;
use App\Http\Helpers\Selector\Selector;

class KeyWordDecorate implements Decorator
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * 装饰数据
     *
     * @param array $collection
     * @param Selector $selector
     */
    public function decorate(array &$collection, Selector $selector)
    {
        if (empty($collection)) return;

        $this->collection = &$collection;

        $commonParser = $this->getCommonParser($selector);

        $associations = $this->getAssociationParser($selector);

        $this->decoratePrimaryKeyWord($commonParser);

        $this->decorateAssociationKeyWord($associations);
    }

    /**
     * 装饰搜索关键字
     *
     * @param CommonParser $commonParser
     */
    private function decoratePrimaryKeyWord(CommonParser $commonParser)
    {
        if (empty($commonParser->getLikes())) return;

        $this->replaceKeyWord($commonParser->getLikes());
    }

    /**
     * 装饰搜索关键字
     *
     * @param AssociationParser $associationParser
     */
    private function decorateAssociationKeyWord(AssociationParser $associationParser)
    {
        $associations = $associationParser->getAssociations();

        if (empty($associations)) return;

        foreach ($associations as $joinItem) {

            if (empty($joinItem->whereLikeConditions)) return;

            $this->replaceKeyWord($joinItem->whereLikeConditions);
        }
    }

    /**
     * @param $likeKeyValue
     */
    private function replaceKeyWord($likeKeyValue)
    {
        foreach ($likeKeyValue as $likeKey => $likeValue) {
            array_walk($this->collection, function (&$item) use ($likeKey, $likeValue) {
                if (isset($item[$likeKey])) {
                    $item[$likeKey] =  str_replace($likeValue, $this->addRedColor($likeValue), $item[$likeKey]);
                }
            });
        }
    }

    /**
     * @param $str
     * @return string
     */
    private function addRedColor($str)
    {
        return '<span style="color:red">' . $str . '</span>';
    }


    /**
     * @param Selector $selector
     * @return \App\Http\Helpers\Selector\Parser\CommonParser
     */
    private function getCommonParser(Selector $selector)
    {
        $commonParser = $selector->getParser(CommonParser::class);

        if ($commonParser != null && $commonParser instanceof CommonParser) {
            return $commonParser;
        }

        throw new \RuntimeException('找不到' . CommonParser::class . '解析器');
    }


    /**
     * @param Selector $selector
     * @return \App\Http\Helpers\Selector\Parser\AssociationParser
     */
    private function getAssociationParser(Selector $selector)
    {
        $associateParser = $selector->getParser(AssociationParser::class);

        if ($associateParser != null && $associateParser instanceof AssociationParser) {
            return $associateParser;
        }

        throw new \RuntimeException('找不到' . AssociationParser::class . '解析器');
    }
}
