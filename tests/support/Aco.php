<?php

namespace PHPAccessControl\UnitTests\support;

class Aco
{
    public static function named($name)
    {
        return new \PHPAccessControl\AccessControledObject\Aco($name);
    }

    public static function postWithCategoryIdEquals5()
    {
        return  CreateAco::name('post')->with(CategoryId::equals5());
    }

    public static function postWithCategoryIdEquals5AndWordCountGreaterThan100()
    {
        return CreateAco::name('post')
                ->with(CategoryId::equals5())
                ->with(Wordcount::greaterThan(100));
    }

    public static function postWithWordCountGreaterThan($x)
    {
        return CreateAco::name('post')
                ->with(Wordcount::greaterThan($x));
    }
}
